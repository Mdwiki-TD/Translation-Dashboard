<?php
//---
// include_once __DIR__ . '/../backend/userinfos_wrap.php';
//---

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use function APICalls\MdwikiSql\fetch_query;

require_once __DIR__ . '/../include_all.php';

$cookieDomain = $_SERVER['SERVER_NAME'] ?? 'localhost';
$secure = ($cookieDomain === 'localhost') ? false : true;

if ($cookieDomain != 'localhost') {
    if (session_status() === PHP_SESSION_NONE) {
        session_name("mdwikitoolforgeoauth");
        // Ensure $domain is defined, fallback to server name
        session_set_cookie_params(0, "/", $cookieDomain, $secure, $secure);
    }
}

function decode_value($value)
{
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }
    $cookieKeyRaw = getenv('COOKIE_KEY') ?: ($_ENV['COOKIE_KEY'] ?? '');
    if (empty($cookieKeyRaw)) {
        return '';
    }
    try {
        $cookieKey = Key::loadFromAsciiSafeString($cookieKeyRaw);
        return Crypto::decrypt($value, $cookieKey);
    } catch (\Throwable $e) {
        return '';
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
            'access_key' => decode_value($result[0]['access_key']),
            'access_secret' => decode_value($result[0]['access_secret'])
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
//---
$username = get_from_cookies('username');
//---
if ($cookieDomain == 'localhost') {
    $username = $_SESSION['username'] ?? '';
} elseif (!empty($username)) {
    $access = get_access_from_db($username);
    if ($access == null) {
        echo ba_alert("No access keys found. Login again.");
        setcookie('username', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => $cookieDomain,
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $username = '';
        unset($_SESSION['username']);
    }
}
//---
$global_username = $username;
//---
define('global_username', $global_username);
$GLOBALS['global_username'] = $global_username;
