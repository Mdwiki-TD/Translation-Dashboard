<?php
//---
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/helps.php';
//---
require_once __DIR__ . '/../actions/access_helps.php';
require_once __DIR__ . '/../actions/html.php';
//---
use function OAuth\Helps\get_from_cookie;
use function Actions\AccessHelps\get_access_from_db;
use function Actions\Html\banner_alert;
//---
$secure = ($_SERVER['SERVER_NAME'] == "localhost") ? false : true;
if ($_SERVER['SERVER_NAME'] != 'localhost') {
	session_name("mdwikitoolforgeoauth");
	session_set_cookie_params(0, "/", $domain, $secure, $secure);
}
//---
$username = get_from_cookie('username');
//---
if ($_SERVER['SERVER_NAME'] == 'localhost') {
	session_start();
	$username = $_SESSION['username'] ?? '';
} elseif (!empty($username)) {
	// ---
	$access_key = get_from_cookie('accesskey');
	$access_secret = get_from_cookie('access_secret');
	// ---
	$access = get_access_from_db($username);
	// ---
	if (empty($access_key) || empty($access_secret) || $access == null) {
		echo banner_alert("No access keys found. Login again.");
		setcookie('username', '', time() - 3600, "/", $domain, true, true);
		$username = '';
	}
}
//---
define('global_username', $username);
//---
function echo_login()
{
	global $username;
	$safeUsername = htmlspecialchars($username); // Escape characters to prevent XSS

	if (empty($username)) {
		echo <<<HTML
			Go to this URL to authorize this tool:<br />
			<a href='auth.php?a=login'>Login</a><br />
		HTML;
	} else {
		echo <<<HTML
			You are authenticated as $safeUsername.<br />
			Continue to <a href='auth.php?a=edit'>edit</a><br>
			<a href='auth.php?a=logout'>logout</a>
		HTML;
	};
	//---
};
