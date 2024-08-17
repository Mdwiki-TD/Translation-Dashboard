<?php
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
include_once __DIR__ . '/helps.php';

use function OAuth\Helps\add_to_cookie;

$u = filter_input(INPUT_GET, 'u', FILTER_SANITIZE_SPECIAL_CHARS);
$allowed_u = [
    "Mina karaca",
    "Mr. Ibrahem"
];
if ($u != '' && in_array($u, $allowed_u)) {
    session_start();
    $_SESSION['username'] = $u;
    //---
    add_to_cookie('username', $u);
    //---
    session_regenerate_id();
    //---
    $return_to = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/Translation_Dashboard/index.php';
    //---
    header("Location: $return_to");
    exit(0);
};

// use function Actions\MdwikiSql\sql_add_user;

if ($_SERVER['SERVER_NAME'] === 'localhost') {
    $fa = $_GET['test'] ?? '';
    // if ($fa != 'xx') {
    // Get the Request Token's details from the session and create a new Token object.
    session_start();
    // ---
    $user = 'Mr. Ibrahem';
    $_SESSION['username'] = $user;
    $_COOKIE['username'] = $user;
    //---
    // add_to_cookie('username', $user);
    //---
    $return_to = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/Translation_Dashboard/index.php';
    //---
    header("Location: $return_to");
    exit(0);
    // };
};
//---
