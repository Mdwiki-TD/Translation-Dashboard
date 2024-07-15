<?php
/*
usage:

include_once __DIR__ . '/vendor_load.php';
*/

if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
require __DIR__ . '/vendor/autoload.php'; // TD

// require(__DIR__ . '/../../vendor/autoload.php');
