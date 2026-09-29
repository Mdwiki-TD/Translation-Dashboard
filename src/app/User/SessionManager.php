<?php
// src/app/User/SessionManager.php

namespace App\User;

/**
 * Owns the PHP session lifecycle: starting it with secure options,
 * and destroying it on logout. No identity or cookie logic lives here.
 */
class SessionManager
{
    public static function ensureStarted(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $sessionOptions = [
            "use_strict_mode"  => true,
            "use_cookies"      => true,
            "use_only_cookies" => true,
            "cookie_httponly"  => true,
            "cookie_samesite"  => "Lax",
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

    public static function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}
