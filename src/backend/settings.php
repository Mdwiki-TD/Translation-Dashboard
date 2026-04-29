<?php

declare(strict_types=1);

namespace OAuth\Settings;

use Defuse\Crypto\Key;

/**
 * @property string $domain
 * @property string $userAgent
 * @property string $oauthUrl
 * @property string $apiUrl
 * @property string $consumerKey
 * @property string $consumerSecret
 * @property string $appEnv
 * @property Key|null $cookieKey
 * @property Key|null $decryptKey
 * @property string $jwtKey
 * @property string $TablesPath
 */
final class Settings
{
    // Private properties — access is controlled via __get()
    public string $domain;
    public string $ServerUrl;
    public string $userAgent;
    public string $oauthUrl;
    public string $apiUrl;
    public string $consumerKey;
    public string $consumerSecret;
    public string $appEnv;
    public ?Key   $cookieKey;
    public ?Key   $decryptKey;
    public string $jwtKey;
    public string $TablesPath;

    private static ?self $instance = null;

    private function __construct()
    {
        $this->domain    = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $this->ServerUrl = $this->generateServerUrl();
        $this->userAgent = 'mdwiki MediaWiki OAuth Client/1.0';
        $this->oauthUrl  = 'https://meta.wikimedia.org/w/index.php?title=Special:OAuth';
        $this->apiUrl    = preg_replace('/index\.php.*/', 'api.php', $this->oauthUrl);

        $appEnv         = $this->envVar('APP_ENV');
        $consumerKey    = $this->envVar('CONSUMER_KEY');
        $consumerSecret = $this->envVar('CONSUMER_SECRET');
        $cookieKey      = $this->envVar('COOKIE_KEY');
        $decryptKey     = $this->envVar('DECRYPT_KEY');
        $jwtKey         = $this->envVar('JWT_KEY');
        $TablesPath = $this->envVar('JSON_TABLES_PATH');

        if ($appEnv === 'production' && (
            empty($consumerKey) || empty($consumerSecret) ||
            empty($cookieKey)   || empty($decryptKey)     || empty($jwtKey)
        )) {
            http_response_code(500);
            error_log('Required configuration directives not found in environment variables!');
            echo 'Required configuration directives not found';
            exit(0);
        }

        $this->appEnv    = $appEnv;
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->cookieKey      = $cookieKey  ? Key::loadFromAsciiSafeString($cookieKey)  : null;
        $this->decryptKey     = $decryptKey ? Key::loadFromAsciiSafeString($decryptKey) : null;

        $this->jwtKey         = $jwtKey;
        $this->TablesPath = $TablesPath;
    }

    /**
     *
     */
    private function generateServerUrl()
    {
        /*
        "SERVER_PORT": "9001",
        "SERVER_NAME": "localhost",
        "HTTP_HOST": "localhost:9001",
        */
        // 1. Detect Protocol: Works for local development and production proxies
        $protocol = 'http';

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            // Standard SSL detection
            $protocol = 'https';
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            // Detection for environments behind a Proxy/Load Balancer (e.g., Nginx, Cloudflare)
            $protocol = 'https';
        }

        // 2. Detect Host: HTTP_HOST captures both domain and port (e.g., localhost:9000)
        // This is OS-agnostic (Works the same on Windows and Linux)
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // 4. Build the final absolute URI
        return $protocol . "://" . $host;
    }
    /**
     */
    public function is_development()
    {
        return $this->appEnv === "development";
    }
    public function is_production()
    {
        return $this->appEnv === "production";
    }
    public function is_testing()
    {
        return $this->appEnv === "testing";
    }
    /**
     * Generates a dynamic Callback URL that works seamlessly on Windows (localhost)
     * and Linux (production) environments.
     * * @param string $path The destination path (e.g., 'auth/callback')
     * @return string The absolute URL including protocol and host
     */
    public function generateCallbackUrl($path = '/auth/callback.php')
    {
        // Normalize Path: Ensure the path starts with a single forward slash
        $path = '/' . ltrim($path, '/');

        // Build the final absolute URI
        return $this->ServerUrl . $path;
    }
    private function envVar(string $key)
    {
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        return "";
    }
    /**
     * Allow reading private properties from outside the class.
     * Mimics the behaviour of readonly properties (PHP 8.1+).
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new \RuntimeException("Undefined setting: {$name}");
    }

    /**
     * Prevent modification from outside the class.
     * Mimics the behaviour of readonly properties (PHP 8.1+).
     *
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        throw new \RuntimeException("Settings are read-only. Cannot set: {$name}");
    }

    /**
     * Returns the single instance of Settings for the lifetime of the request.
     * Equivalent to @lru_cache(maxsize=1) in Python.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // Prevent cloning and unserialization of the singleton instance
    private function __clone() {}

    public function __wakeup(): void
    {
        throw new \RuntimeException('Cannot unserialize a singleton.');
    }
}
