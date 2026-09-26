<?php

namespace App\Render\TestPrint;

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
