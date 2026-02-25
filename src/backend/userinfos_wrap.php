<?php
//---
// include_once __DIR__ . '/../backend/userinfos_wrap.php';
//---

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use function APICalls\MdwikiSql\fetch_query;

$vendorAutoload = dirname(__DIR__) . '/vendor/autoload.php';

if (!file_exists($vendorAutoload)) {
    $vendorAutoload = dirname(dirname(__DIR__)) . '/vendor/autoload.php';
}

if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
} else {
    die("Vendor autoload not found. Please run 'composer install' in the project root.");
}

$cookieDomain = $_SERVER['SERVER_NAME'] ?? 'localhost';
$secure = ($cookieDomain === 'localhost') ? false : true;
// ---
if ($cookieDomain != 'localhost') {
    if (session_status() === PHP_SESSION_NONE) {
        session_name("mdwikitoolforgeoauth");
        // Ensure $domain is defined, fallback to server name
        session_set_cookie_params(0, "/", $cookieDomain, $secure, $secure);
    }
}

function de_code_value($value)
{
    // ---
    if (empty(trim($value))) {
        return "";
    }
    // ---
    $cookieKey = getenv('COOKIE_KEY') ?: ($_ENV['COOKIE_KEY'] ?? '');
    $cookieKey      = $cookieKey  ? Key::loadFromAsciiSafeString($cookieKey)  : null;
    try {
        $value = Crypto::decrypt($value, $cookieKey);
    } catch (\Exception $e) {
        $value = "";
    }
    return $value;
}

function get_access_from_dbs($user)
{
    // Validate and sanitize username
    $user = trim($user);

    // Query to get access_key and access_secret for the user
    $query = <<<SQL
        SELECT access_key, access_secret
        FROM access_keys
        WHERE user_name = ?;
    SQL;

    // تنفيذ الاستعلام وتمرير اسم المستخدم كمعامل
    $result = fetch_query($query, [$user]);

    // التحقق مما إذا كان قد تم العثور على نتائج

    if (!$result) {
        // إذا لم يتم العثور على نتيجة، إرجاع null أو يمكنك تخصيص رد معين
        return null;
    }

    $result = $result[0];
    // ---
    return [
        'access_key' => de_code_value($result['access_key']),
        'access_secret' => de_code_value($result['access_secret'])
    ];
}

function get_from_cookies($key)
{
    if (isset($_COOKIE[$key])) {
        $value = de_code_value($_COOKIE[$key]);
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
    // ---
    $access = get_access_from_dbs($username);
    // ---
    if ($access == null) {
        echo ba_alert("No access keys found. Login again.");
        setcookie('username', '', time() - 3600, "/", $cookieDomain, true, true);
        $username = '';
        unset($_SESSION['username']);
    }
}
//---
$global_username = $username;
//---
define('global_username', $global_username);
$GLOBALS['global_username'] = $global_username;
