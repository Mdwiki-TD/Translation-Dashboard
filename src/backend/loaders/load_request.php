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

function load_request($s_campaign_input_list, $allow_whole_translate)
{
    //---
    $errors = [];
    //---
    $test = htmlspecialchars($_GET['test'] ?? '', ENT_QUOTES, 'UTF-8');
    $doit = htmlspecialchars($_GET['doit'] ?? '', ENT_QUOTES, 'UTF-8');
    $code = htmlspecialchars($_GET['code'] ?? '', ENT_QUOTES, 'UTF-8');
    $filter_sparql = !empty($_GET['filter_sparql'] ?? '') ? true : false;
    //---
    if ($code == 'undefined') $code = "";
    //---
    $code = trim($code);
    //---
    $code = LangsTables::$L_lang_to_code[$code] ?? $code;
    //---
    $code_lang_name = LangsTables::$L_code_to_lang[$code] ?? '';
    //---
    $cat  = htmlspecialchars($_GET['cat'] ?? '', ENT_QUOTES, 'UTF-8');
    if ($cat == 'undefined') $cat = "";
    //---
    $camp = htmlspecialchars($_GET['camp'] ?? '', ENT_QUOTES, 'UTF-8');
    //---
    $camp = trim($camp);
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
    $errors = [];
    //---
    if (empty($code_lang_name) && !empty($code)) {
        $errors[] = "code ($code) not valid wiki.";
        $code = "";
    } elseif (!empty($code)) {
        $_SESSION['code'] = $code;
    }
    //---
    if (!in_array($camp, $s_campaign_input_list)) {
        $errors[] = "camp ($camp) not valid.";
        $camp = "";
    }
    //---
    if ($allow_whole_translate == '0') {
        $tra_type = 'lead';
    }
    //---
    return [
        'test' => !empty($test),
        'doit' => $doit,
        'code' => $code,
        'cat' => $cat,
        'camp' => $camp,
        'tra_type' => $tra_type,
        'filter_sparql' => $filter_sparql,
        'code_lang_name' => $code_lang_name,
        'errors' => $errors,
    ];
}
