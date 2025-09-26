<?php

namespace Loaders\LoadRequest;
/*
Usage:
use function Loaders\LoadRequest\load_request;
*/

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

use Tables\SqlTables\TablesSql;
use Tables\Langs\LangsTables;

function load_request()
{
    //---
    $test = htmlspecialchars($_GET['test'] ?? '', ENT_QUOTES, 'UTF-8');
    $doit = htmlspecialchars($_GET['doit'] ?? '', ENT_QUOTES, 'UTF-8');
    $code = htmlspecialchars($_GET['code'] ?? '', ENT_QUOTES, 'UTF-8');
    //---
    if ($code == 'undefined') $code = "";
    //---
    $code = LangsTables::$L_lang_to_code[$code] ?? $code;
    $code_lang_name = LangsTables::$L_code_to_lang[$code] ?? '';
    //---
    $cat  = htmlspecialchars($_GET['cat'] ?? '', ENT_QUOTES, 'UTF-8');
    if ($cat == 'undefined') $cat = "";
    //---
    $camp = htmlspecialchars($_GET['camp'] ?? '', ENT_QUOTES, 'UTF-8');
    //---
    if (empty($cat) && !empty($camp)) {
        $cat = TablesSql::$s_camp_to_cat[$camp] ?? $cat;
    }
    //---
    if (!empty($cat) && empty($camp)) {
        $camp = TablesSql::$s_cat_to_camp[$cat] ?? $camp;
    }
    // if (empty($cat)) $cat = "RTT";
    //---
    $tra_type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
    //---
    $doit = $doit !== "";
    //---
    if (empty($code_lang_name)) $doit = false;
    //---
    return [
        'test' => $test !== "",
        'doit' => $doit,
        'code' => $code,
        'cat' => $cat,
        'camp' => $camp,
        'tra_type' => $tra_type,
        'code_lang_name' => $code_lang_name
    ];
}
