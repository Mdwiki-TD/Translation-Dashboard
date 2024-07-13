<?php
// user_account_new.php

if (isset($_REQUEST['test'])) {
    $print_t = true;
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
// ---
include_once __DIR__ . '/td_config.php';
// ---
use function Infos\TdConfig\Read_ini_file;
// ---

$ini = Read_ini_file('user.ini');
//---
$username_x = $ini['botusername'];
$password = $ini['botpassword'];

$bot_username = $ini['botusername'];
$bot_password = $ini['botpassword'];

$my_username = $ini['my_username'];
$my_password = $ini['my_password'];

$mdwiki_pass = $ini['mdwiki_pass'];

$lgname_enwiki = $ini['lgname_enwiki'];
$lgpass_enwiki = $ini['lgpass_enwiki'];

$qs_token = $ini['qs_token'];

$user_agent = $ini['user_agent'];
