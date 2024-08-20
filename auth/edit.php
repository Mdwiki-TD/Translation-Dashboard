<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/send_edit.php';
include_once __DIR__ . '/helps.php';

use function OAuth\Helps\get_from_cookie;

$title   = $_GET['title'] ?? 'وب:ملعب';
$text    = $_GET['text'] ?? 'new!new!';
$lang    = $_GET['lang'] ?? 'ar';
$summary = $_GET['summary'] ?? 'h!';


$access_key = get_from_cookie('accesskey');
$access_secret = get_from_cookie('access_secret');

$editit = do_edit($title, $text, $summary, $lang, $access_key, $access_secret);

echo "\n== You made an edit ==<br>";

print(json_encode($editit, JSON_PRETTY_PRINT));
