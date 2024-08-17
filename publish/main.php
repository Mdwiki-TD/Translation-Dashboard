<?php
include_once __DIR__ . '/../auth/helps.php';
include_once __DIR__ . '/helps.php';
include_once __DIR__ . '/send_edit.php';

// use function Publish\Helps\get_access_from_db;
use function Publish\Edit\send_edit;
use function OAuth\Helps\get_from_cookie;

// $user   = $_GET['user'];
$title  = $_GET['title'];
$text   = $_GET['text'];
$lang   = $_GET['lang'];

$username = get_from_cookie('username');

// if ($user != $username) {
//     echo ("error:  user name mismatch");
//     exit;
// }

// $access = get_access_from_db($user);

$editit = send_edit($title, $text, $summary, $lang);

print_r($editit);
