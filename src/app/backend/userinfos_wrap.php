<?php

use Defuse\Crypto\Crypto;
use function APICalls\MdwikiSql\fetch_query;
use function SQLorAPI\Funcs\get_coordinators;
use OAuth\Settings\Settings;

function get_key(Settings $settings, string $key_type = "cookie")
{
    $use_key  = ($key_type === "decrypt") ? $settings->decryptKey : $settings->cookieKey;

    return $use_key;
}

function decode_value(string $value, $use_key): string
{
    if (empty(trim($value))) return "";

    if ($use_key === null) return "";

    try {
        return Crypto::decrypt($value, $use_key);
    } catch (\Throwable $e) {
        return "";
    }
}

function get_access_from_db(string $user, $decrypt_key): array
{
    $user = trim($user);

    $query = <<<SQL
        SELECT access_key, access_secret
        FROM access_keys
        WHERE user_name = ? or user_name_hash = ?;
    SQL;

    $result = fetch_query($query, [$user, hash('sha256', $user)], true);

    if ($result) {
        return [
            'access_key' => decode_value($result[0]['access_key'], $decrypt_key),
            'access_secret' => decode_value($result[0]['access_secret'], $decrypt_key)
        ];
    }
    return [];
}

function get_from_cookies(string $key, $cookie_key): string
{
    if (isset($_COOKIE[$key])) {
        $value = decode_value($_COOKIE[$key], $cookie_key);
    } else {
        // echo "key: $key<br>";
        $value = "";
    };
    if ($key == "username") {
        $value = str_replace("+", " ", $value);
    };
    return $value;
}

function ba_alert(string $text): string
{
    return <<<HTML
	<div class='container'>
		<div class="alert alert-danger" role="alert">
			<i class="bi bi-exclamation-triangle"></i> $text
		</div>
	</div>
	HTML;
}

/**
 * Helper function to remove the username cookie safely.
 */
function clear_user_cookie(string $domain): void
{
    setcookie('username', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'domain'   => $domain,
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function load_user(Settings $settings): array
{

    // 1. Initialize session if not already active
    if (session_status() === PHP_SESSION_NONE) {
        // Set custom session configuration only in production environment
        if ($settings->is_production()) {
            session_name("mdwikitoolforgeoauth");
            session_set_cookie_params(0, "/", $settings->domain, true, true);
        }

        // Start the PHP session
        session_start();
    }

    $cookie_key  = get_key($settings, "cookie");
    // 2. Fetch initial username based on environment
    $username = get_from_cookies('username', $cookie_key);

    // Override with session data in development environment
    if ($settings->is_development()) {
        $username = $_SESSION['username'] ?? $username;
    }

    // 3. Validate user access in production
    if ($settings->is_production() && !empty($username)) {
        $decrypt_key  = get_key($settings, "decrypt");
        $access = get_access_from_db($username, $decrypt_key);

        if (empty($access)) {
            echo ba_alert("No access keys found. Login again.");

            // Clear identity
            clear_user_cookie($settings->domain);
            unset($_SESSION['username']);
            $username = '';
        }
    }

    // 4. Set global variables safely
    $GLOBALS['global_username'] = $username;

    if (!defined('global_username')) {
        define('global_username', $username);
    }

    $user_is_coordinator = false;

    if (!empty($username)) {
        $coordinators = array_column(get_coordinators(), 'is_active', 'username');
        $user_is_coordinator = (($coordinators[$username] ?? 0) == 1);

        $GLOBALS['user_is_coordinator'] = $user_is_coordinator;
    }

    return [$username, $user_is_coordinator];
}
