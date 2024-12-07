<?php

namespace Actions\Functions;
/*
Usage:
use function Actions\Functions\load_request;
use function Actions\Functions\escape_string;
use function Actions\Functions\strstartswithn;
use function Actions\Functions\strendswith;
use function Actions\Functions\test_print;
*/

$print_t = false;

if (isset($_REQUEST['test'])) {
    $print_t = true;
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

define('print_te', $print_t);

include_once __DIR__ . '/html.php';
include_once __DIR__ . '/wiki_api.php';
include_once __DIR__ . '/mdwiki_api.php';
include_once __DIR__ . '/td_api.php';
include_once __DIR__ . '/mdwiki_sql.php';

use function SQLorAPI\Get\get_coordinator;

function load_request()
{
    global $lang_to_code, $code_to_lang, $camp_to_cat, $cat_to_camp;
    //---
    $code = $_REQUEST['code'] ?? '';
    //---
    if ($code == 'undefined') $code = "";
    //---
    $code = $lang_to_code[$code] ?? $code;
    $code_lang_name = $code_to_lang[$code] ?? '';
    //---
    $cat  = $_REQUEST['cat'] ?? '';
    if ($cat == 'undefined') $cat = "";
    //---
    $camp = $_REQUEST['camp'] ?? '';
    //---
    if (empty($cat) && !empty($camp)) {
        $cat = $camp_to_cat[$camp] ?? $cat;
    }
    //---
    if (!empty($cat) && empty($camp)) {
        $camp = $cat_to_camp[$cat] ?? $camp;
    }
    // if (empty($cat)) $cat = "RTT";
    //---
    return [
        'code' => $code,
        'cat' => $cat,
        'camp' => $camp,
        'code_lang_name' => $code_lang_name
    ];
}

function escape_string($unescaped_string)
{
    // Alternative mysql_real_escape_string without mysql connection
    $replacementMap = [
        "\0" => "\\0",
        "\n" => "",
        "\r" => "",
        "\t" => "",
        chr(26) => "\\Z",
        chr(8) => "\\b",
        '"' => '\"',
        "'" => "\'",
        '_' => "\_",
        "%" => "\%",
        '\\' => '\\\\'
    ];

    return \strtr($unescaped_string, $replacementMap);
}
function strstartswithn($text, $word)
{
    return strpos($text, $word) === 0;
}

function strendswith($text, $end)
{
    return substr($text, -strlen($end)) === $end;
}

function test_print($s)
{
    if (print_te && gettype($s) == 'string') {
        echo "\n<br>\n$s";
    } elseif (print_te) {
        echo "\n<br>\n";
        print_r($s);
    }
}

$coordinators = get_coordinator();
// ---
$coordinators = array_map('current', $coordinators);

// var_dump(json_encode($coordinators2, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
