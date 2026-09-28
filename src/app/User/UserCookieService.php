<?php
// src/app/User/UserCookieService.php

namespace App\User;

use App\Settings;

/**
 * Owns everything related to the "username" cookie: reading it,
 * decoding it, writing a fresh (encoded) value, and clearing it.
 * No session state, no DB access, no coordinator logic.
 */
class UserCookieService
{
    private const COOKIE_NAME = "username";

    public function __construct(private Settings $settings)
    {
    }

    public function read(): string
    {
        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return "";
        }

        $cookieKey = $this->settings->getKey("cookie");
        $value = $this->settings->decodeValue($_COOKIE[self::COOKIE_NAME], $cookieKey);

        return str_replace("+", " ", $value);
    }

    public function write(string $username): bool
    {
        $cookieKey = $this->settings->getKey("cookie");
        $value = $this->settings->encodeValue($username, $cookieKey);

        if ($value === "") {
            return false;
        }

        $twoYears = time() + 60 * 60 * 24 * 365 * 2;
        $secure   = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";

        return setcookie(
            self::COOKIE_NAME,
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
    }

    public function clear(): void
    {
        setcookie(self::COOKIE_NAME, "", [
            "expires"  => time() - 3600,
            "path"     => "/",
            "domain"   => $this->settings->domain,
            "secure"   => true,
            "httponly" => true,
            "samesite" => "Lax",
        ]);
    }
}
