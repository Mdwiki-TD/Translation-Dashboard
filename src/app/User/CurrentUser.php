<?php
// src/app/User/CurrentUser.php

namespace App\User;

use App\Settings;
use App\MdwikiSql\Database;

use App\User\UserCookieService;
use App\User\SessionManager;
use App\User\AccessKeyRepository;
use App\User\CoordinatorRepository;

/**
 * Represents the current user. Coordinates SessionManager, UserCookieService,
 * AccessKeyRepository and CoordinatorRepository to resolve identity and
 * expose it to the rest of the app. Holds no session/cookie/SQL logic of
 * its own anymore — that lives in the collaborators below.
 */
class CurrentUser
{
    private static ?self $instance = null;

    private Settings $settings;
    private UserCookieService $cookies;
    private AccessKeyRepository $accessKeys;
    private CoordinatorRepository $coordinators;

    private string $username = "";
    private bool $isCoordinator = false;
    private ?string $alertMessage = null;

    public function __construct(
        Settings $settings,
        ?UserCookieService $cookies = null,
        ?AccessKeyRepository $accessKeys = null,
        ?CoordinatorRepository $coordinators = null
    ) {
        $this->settings = $settings;

        // Collaborators are injectable (for testing) but default to the
        // real implementations so existing call sites keep working.
        $db = new Database('DB_NAME');
        $this->cookies      = $cookies ?? new UserCookieService($settings);
        $this->accessKeys   = $accessKeys ?? new AccessKeyRepository($db, $settings);
        $this->coordinators = $coordinators ?? new CoordinatorRepository($db);

        SessionManager::ensureStarted();
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

    /**
     * Kept for backward compatibility with existing call sites that call
     * CurrentUser::ensureSessionStarted() directly.
     */
    public static function ensureSessionStarted(): void
    {
        SessionManager::ensureStarted();
    }

    public function destroy(): void
    {
        SessionManager::destroy();
        $this->cookies->clear();
        $this->username = "";
        $this->isCoordinator = false;
    }

    public function clearUserCookie(): void
    {
        $this->cookies->clear();
    }

    private function resolveUsername(): void
    {
        $username = $this->cookies->read();

        if ($this->settings->isDevelopment()) {
            $username = $_SESSION["username"] ?? $username;
        }

        if ($this->settings->isProduction() && $username !== "") {
            $access = $this->accessKeys->findByUser($username);

            if (empty($access)) {
                $this->alertMessage = "No access keys found. Login again.";
                $this->cookies->clear();
                unset($_SESSION["username"]);
                $username = "";
            }
        }

        $this->username = $username;
    }

    private function resolveCoordinatorStatus(): void
    {
        $this->isCoordinator = $this->coordinators->isCoordinator($this->username);
    }

    public function addUsernameToCookies(string $username): void
    {
        $this->cookies->write($username);
    }

    public function saveUserData(string $user, string $accessKey, string $accessSecret): void
    {
        $this->accessKeys->saveUserData($user, $accessKey, $accessSecret);
    }
}
