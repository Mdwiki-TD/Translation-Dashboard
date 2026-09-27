<?php
// src/app/CurrentUser.php

namespace App\User;

use App\Settings;
use App\MdwikiSql\Database;
use function App\SQLorAPI\Funcs\get_coordinators;

/**
 * Represents the current user: handles session initialization, reading
 * identity from cookies/session, validating access in the database,
 * and determining coordinator status.
 */

class CurrentUser
{
    private static ?self $instance = null;

    private Settings $settings;
    private Database $db;

    private string $username = "";
    private bool $isCoordinator = false;
    private ?string $alertMessage = null;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        $this->db = new Database('DB_NAME');
        $this->ensureSessionStarted();
        $this->resolveUsername();
        $this->resolveCoordinatorStatus();
        self::$instance = $this;
    }

    public static function getInstance(?Settings $settings = null): self
    {
        if (self::$instance === null) {
            $settings = $settings ?? Settings::getInstance();
            self::$instance = new self($settings);
        }
        return self::$instance;
    }

    // ------------------------------------------------------------------
    // Public API
    // ------------------------------------------------------------------

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isCoordinator(): bool
    {
        return $this->isCoordinator;
    }

    public function isLoggedIn(): bool
    {
        return $this->username !== "";
    }

    /**
     * The alert message resulting from a failed validation (if any),
     * instead of echoing it directly inside the class. Leave the actual
     * rendering to the view layer.
     */
    public function getAlertMessage(): ?string
    {
        return $this->alertMessage;
    }

    // ------------------------------------------------------------------
    // Internal helpers
    // ------------------------------------------------------------------

    public static function ensureSessionStarted(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $sessionOptions = [
            "use_strict_mode"   => true,
            "use_cookies"       => true,
            "use_only_cookies"  => true,
            "cookie_httponly"   => true,
            "cookie_samesite"   => "Lax",
        ];

        // Enable secure flag in production (HTTPS)
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") {
            $sessionOptions["cookie_secure"] = true;
        }

        if (headers_sent()) {
            error_log("OAuth Error: Cannot start session, headers already sent.");
            return;
        }

        session_start($sessionOptions);

        if (session_id() === '') {
            error_log("OAuth Error: Session failed to start.");
        }
    }

    private function getFromCookies(string $key): string
    {
        if (!isset($_COOKIE[$key])) {
            return "";
        }

        $cookieKey = $this->settings->getKey("cookie");
        $value = $this->settings->decodeValue($_COOKIE[$key], $cookieKey);

        if ($key === "username") {
            $value = str_replace("+", " ", $value);
        }

        return $value;
    }

    private function getAccessFromDb(string $user): array
    {
        $user = trim($user);

        $query = <<<SQL
            SELECT access_key, access_secret
            FROM access_keys
            WHERE user_name = ? or user_name_hash = ?;
        SQL;

        $result = $this->db->fetchquery($query, [$user, hash("sha256", $user)]);

        if (!$result) {
            return [];
        }

        $decryptKey = $this->settings->getKey("decrypt");
        return [
            "access_key"    => $this->settings->decodeValue($result[0]["access_key"], $decryptKey),
            "access_secret" => $this->settings->decodeValue($result[0]["access_secret"], $decryptKey),
        ];
    }

    public function Logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->clearUserCookie();
    }
    public function clearUserCookie(): void
    {
        setcookie("username", "", [
            "expires"  => time() - 3600,
            "path"     => "/",
            "domain"   => $this->settings->domain,
            "secure"   => true,
            "httponly" => true,
            "samesite" => "Lax",
        ]);
    }

    private function resolveUsername(): void
    {
        $username   = $this->getFromCookies("username");

        if ($this->settings->isDevelopment()) {
            $username = $_SESSION["username"] ?? $username;
        }

        if ($this->settings->isProduction() && $username !== "") {
            $access      = $this->getAccessFromDb($username);

            if (empty($access)) {
                $this->alertMessage = "No access keys found. Login again.";
                $this->clearUserCookie();
                unset($_SESSION["username"]);
                $username = "";
            }
        }

        $this->username = $username;
    }
    public function addUsernameToCookies(string $username): void
    {
        $_SESSION["username"] = $username;

        $cookieKey = $this->settings->getKey("cookie");
        $value      = $this->settings->encodeValue($username, $cookieKey);

        if ($value === "") {
            return;
        }

        $twoYears = time() + 60 * 60 * 24 * 365 * 2;
        $secure   = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";

        setcookie(
            "username",
            $value,
            [
                "expires"  => $twoYears,
                "path"     => "/",
                "domain"   => $this->settings->domain,
                "secure"   => $secure,
                "httponly" => true,
                "samesite" => "Lax",
            ]
        );

        $this->username = $username;

        $this->resolveCoordinatorStatus();
    }
    private function sqlAddUser(string $userName): bool
    {
        $query = <<<SQL
            INSERT INTO users (username) SELECT ?
            WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = ?)
        SQL;

        return $this->db->executequery($query, [$userName, $userName]);
    }

    private function addAccessToDb(string $user, string $accessKey, string $accessSecret): bool
    {
        $decryptKey = $this->settings->getKey("decrypt");

        $params = [
            $user,
            hash("sha256", $user),
            $this->settings->encodeValue($accessKey, $decryptKey),
            $this->settings->encodeValue($accessSecret, $decryptKey),
        ];

        // ---
        // user_name_hash = SHA2(user_name, 256)
        // ---
        $query = <<<SQL
            INSERT INTO access_keys (user_name, user_name_hash, access_key, access_secret)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                access_key = VALUES(access_key),
                access_secret = VALUES(access_secret),
                updated_at = NOW();
        SQL;

        return $this->db->executequery($query, $params);
    }

    public function addUserData(string $user, string $accessKey, string $accessSecret): void
    {
        $user = trim($user);

        $userAdded = $this->sqlAddUser($user);
        $accessAdded = $this->addAccessToDb($user, $accessKey, $accessSecret);

        if (!$userAdded || !$accessAdded) {
            throw new \RuntimeException("Failed to write user data or access keys to database.");
        }
    }

    private function resolveCoordinatorStatus(): void
    {
        if ($this->username === "") {
            return;
        }

        $coordinators = array_column(get_coordinators(), "is_active", "username");
        $this->isCoordinator = (($coordinators[$this->username] ?? 0) == 1);
    }
}
