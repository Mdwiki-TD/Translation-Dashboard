<?php

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");

include_once __DIR__ . '/../vendor_load.php';
include_once __DIR__ . '/../auth/config.php';
include_once __DIR__ . '/../auth/helps.php';
include_once __DIR__ . '/helps.php';
include_once __DIR__ . '/get_token.php';

use function Publish\GetToken\get_cxtoken;
use function Publish\Helps\get_access_from_db;
use function OAuth\Helps\add_to_cookie;
use function OAuth\Helps\get_from_cookie;

$wiki    = $_GET['wiki'] ?? '';
$user    = $_GET['user'] ?? '';
$ty      = $_GET['ty'] ?? '';

if (empty($wiki) || empty($user)) {
    print(json_encode(['error' => 'wiki or user is empty']));
    exit(1);
}
$cx_cookie_key = "cxtoken_$user";
$in_cookie = get_from_cookie($cx_cookie_key);
if (!empty($in_cookie)) {
    $cxtoken = json_decode($in_cookie);
    if (isset($cxtoken->jwt)) {
        print(json_encode($cxtoken, JSON_PRETTY_PRINT));
        exit(0);
    }
}

$access = get_access_from_db($user);

if ($access == null) {
    $cxtoken = ['error' => 'no access', 'username' => $user];
    // exit(1);
} else {
    $access_key = $access['access_key'];
    $access_secret = $access['access_secret'];
    // $text = get_medwiki_text($title);

    $cxtoken = get_cxtoken($wiki, $access_key, $access_secret);
    if (isset($cxtoken->jwt)) {
        add_to_cookie($cx_cookie_key, json_encode($cxtoken), time() + 3600);
    }
}

print(json_encode($cxtoken, JSON_PRETTY_PRINT));
