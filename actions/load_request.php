<?php

namespace Actions\LoadRequest;
/*
Usage:
use function Actions\LoadRequest\load_request;
*/

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

include_once __DIR__ . '/html.php';
include_once __DIR__ . '/wiki_api.php';
include_once __DIR__ . '/mdwiki_api.php';
include_once __DIR__ . '/td_api.php';
include_once __DIR__ . '/mdwiki_sql.php';
include_once __DIR__ . '/../api_or_sql/index.php';

use Tables\SqlTables\TablesSql;
use Tables\Langs\LangsTables;

function load_request()
{
    //---
    $code = $_GET['code'] ?? '';
    //---
    if ($code == 'undefined') $code = "";
    //---
    $code = LangsTables::$L_lang_to_code[$code] ?? $code;
    $code_lang_name = LangsTables::$L_code_to_lang[$code] ?? '';
    //---
    $cat  = $_GET['cat'] ?? '';
    if ($cat == 'undefined') $cat = "";
    //---
    $camp = $_GET['camp'] ?? '';
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
    return [
        'code' => $code,
        'cat' => $cat,
        'camp' => $camp,
        'code_lang_name' => $code_lang_name
    ];
}
