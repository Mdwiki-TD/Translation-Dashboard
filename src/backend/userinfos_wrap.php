<?php

use Defuse\Crypto\Crypto;
use function APICalls\MdwikiSql\fetch_query;
use OAuth\Settings\Settings;

require_once __DIR__ . '/../include_all.php';

$settings = Settings::getInstance();

if ($settings->is_production()) {
    if (session_status() === PHP_SESSION_NONE) {
        session_name("mdwikitoolforgeoauth");
        // Ensure $domain is defined, fallback to server name
        session_set_cookie_params(0, "/", $settings->domain, true, true);
    }
}

function decode_value($value, $key_type = "cookie")
{
    if (empty(trim($value))) return "";

    $settings = Settings::getInstance();
    $use_key  = ($key_type === "decrypt") ? $settings->decryptKey : $settings->cookieKey;

    if ($use_key === null) return "";

    try {
        return Crypto::decrypt($value, $use_key);
    } catch (\Throwable $e) {
        return "";
    }
}

function get_access_from_db($user)
{
    $user = trim($user);

    $query = <<<SQL
        SELECT access_key, access_secret
        FROM access_keys
        WHERE user_name = ? or user_name_hash = ?;
    SQL;

    $result = fetch_query($query, [$user, hash('sha256', $user)]);

    if ($result) {
        return [
            'access_key' => decode_value($result[0]['access_key'], "decrypt"),
            'access_secret' => decode_value($result[0]['access_secret'], "decrypt")
        ];
    }
    return [];
}

function get_from_cookies($key)
{
    if (isset($_COOKIE[$key])) {
        $value = decode_value($_COOKIE[$key]);
    } else {
        // echo "key: $key<br>";
        $value = "";
    };
    if ($key == "username") {
        $value = str_replace("+", " ", $value);
    };
    return $value;
}

function ba_alert($text)
{
    return <<<HTML
	<div class='container'>
		<div class="alert alert-danger" role="alert">
			<i class="bi bi-exclamation-triangle"></i> $text
		</div>
	</div>
	HTML;
}

if (session_status() === PHP_SESSION_NONE) session_start();

$username = get_from_cookies('username');

if ($settings->is_development()) {
    $username = $_SESSION['username'] ?? '';
}

if ($settings->is_production()) {
    $access = get_access_from_db($username);
    if (empty($access)) {
        echo ba_alert("No access keys found. Login again.");
        setcookie('username', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => $settings->domain,
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $username = '';
        unset($_SESSION['username']);
    }
}

$global_username = $username;

define('global_username', $global_username);
$GLOBALS['global_username'] = $global_username;
