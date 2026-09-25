<?php

namespace App\User;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use App\Settings\Settings;
use function App\MdwikiSql\fetch_query;
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

    private string $username = "";
    private bool $isCoordinator = false;
    private ?string $alertMessage = null;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
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

    private function ensureSessionStarted(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $sessionOptions = [
            "use_strict_mode"   => true,
            "use_cookies"       => true,
            "use_only_cookies"  => true,
            "cookie_httponly"   => true,
            "cookie_samesite"   => "Strict",
        ];

        // Enable secure flag in production (HTTPS)
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") {
            $sessionOptions["cookie_secure"] = true;
        }
        // Start the PHP session
        if (!headers_sent()) {
            session_start($sessionOptions);
        }
    }

    private function getKey(string $keyType = "cookie"): ?Key
    {
        return $keyType === "decrypt"
            ? $this->settings->decryptKey
            : $this->settings->cookieKey;
    }

    private function decodeValue(string $value, ?Key $useKey): string
    {
        if ($useKey === null || trim($value) === "") {
            return "";
        }

        try {
            return Crypto::decrypt($value, $useKey);
        } catch (\Throwable $e) {
            return "";
        }
    }

    private function getFromCookies(string $key, ?Key $cookieKey): string
    {
        if (!isset($_COOKIE[$key])) {
            return "";
        }

        $value = $this->decodeValue($_COOKIE[$key], $cookieKey);

        if ($key === "username") {
            $value = str_replace("+", " ", $value);
        }

        return $value;
    }

    private function getAccessFromDb(string $user, ?Key $decryptKey): array
    {
        $user = trim($user);

        $query = <<<SQL
            SELECT access_key, access_secret
            FROM access_keys
            WHERE user_name = ? or user_name_hash = ?;
        SQL;

        $result = fetch_query($query, [$user, hash("sha256", $user)], true);

        if (!$result) {
            return [];
        }

        return [
            "access_key"    => $this->decodeValue($result[0]["access_key"], $decryptKey),
            "access_secret" => $this->decodeValue($result[0]["access_secret"], $decryptKey),
        ];
    }

    private function clearUserCookie(): void
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
        $cookieKey = $this->getKey("cookie");
        $username   = $this->getFromCookies("username", $cookieKey);

        if ($this->settings->isDevelopment()) {
            $username = $_SESSION["username"] ?? $username;
        }

        if ($this->settings->isProduction() && $username !== "") {
            $decryptKey = $this->getKey("decrypt");
            $access      = $this->getAccessFromDb($username, $decryptKey);

            if (empty($access)) {
                $this->alertMessage = "No access keys found. Login again.";
                $this->clearUserCookie();
                unset($_SESSION["username"]);
                $username = "";
            }
        }

        $this->username = $username;
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
