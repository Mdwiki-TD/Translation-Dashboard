<?php

namespace TD\Render\TestPrint;
/*
Usage:
use function TD\Render\TestPrint\test_print;
*/

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function test_print($s)
{
    if (isset($_COOKIE['test']) && $_COOKIE['test'] == 'x') {
        return;
    }
    $print_t = (isset($_REQUEST['test']) || isset($_COOKIE['test'])) ? true : false;
    if ($print_t && gettype($s) == 'string') {
        echo "\n<br>\n$s";
    } elseif ($print_t) {
        echo "\n<br>\n";
        print_r($s);
    }
}
