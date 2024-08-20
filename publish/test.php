<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/../actions/mdwiki_sql.php';
include_once __DIR__ . '/helps.php';

use function Publish\Helps\get_access_from_db;

$user = $_GET['user'];

$t = get_access_from_db($user);

print(json_encode($t));
