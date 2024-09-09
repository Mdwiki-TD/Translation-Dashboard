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

$wiki    = $_GET['wiki'] ?? '';
$user    = $_GET['user'] ?? '';
$ty      = $_GET['ty'] ?? '';

if (empty($wiki) || empty($user)) {
    print(json_encode(['error' => 'wiki or user is empty']));
    exit(1);
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
}

print(json_encode($cxtoken, JSON_PRETTY_PRINT));
