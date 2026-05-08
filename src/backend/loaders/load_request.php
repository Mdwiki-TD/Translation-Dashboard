<?php

namespace Loaders\LoadRequest;
/*
Usage:
use function Loaders\LoadRequest\load_request;
*/

if (isset($_REQUEST["test"]) || isset($_COOKIE["test"])) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}

use function SQLorAPI\GetDataTab\get_td_or_sql_categories;
use function Tables\Langs\get_lang_code;
use function Tables\Langs\get_lang_title;

function load_request($campaigns_input_list, $allow_whole_translate)
{
    //---
    $errors = [];
    //---
    $test = htmlspecialchars($_GET["test"] ?? "", ENT_QUOTES, "UTF-8");
    $doit = htmlspecialchars($_GET["doit"] ?? "", ENT_QUOTES, "UTF-8");
    $code = htmlspecialchars($_GET["code"] ?? "", ENT_QUOTES, "UTF-8");
    $filter_sparql = !empty($_GET["filter_sparql"] ?? "") ? true : false;
    //---
    if ($code == "undefined") $code = "";
    //---
    $code = trim($code);
    //---
    $code = get_lang_code($code) ?? $code;
    $code_lang_name = get_lang_title($code) ?? "";
    //---
    $cat  = htmlspecialchars($_GET["cat"] ?? "", ENT_QUOTES, "UTF-8");
    if ($cat == "undefined") $cat = "";
    //---
    $camp = htmlspecialchars($_GET["camp"] ?? "", ENT_QUOTES, "UTF-8");
    //---
    $camp = trim($camp);
    //---
    $categories_tab = get_td_or_sql_categories();
    //---
    if (empty($cat) && !empty($camp)) {
        $camps_data = array_column($categories_tab, "category", "campaign");
        $cat = $camps_data[$camp] ?? $cat;
    }
    //---
    if (!empty($cat) && empty($camp)) {
        $cats_data = array_column($categories_tab, "campaign", "category");
        $camp = $cats_data[$cat] ?? $camp;
    }
    //---
    $tra_type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
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
        $_SESSION["code"] = $code;
    }
    //---
    if ($camp && !in_array($camp, $campaigns_input_list)) {
        $errors[] = "camp ($camp) not valid.";
        $camp = "";
    }
    //---
    if ($allow_whole_translate == "0") {
        $tra_type = "lead";
    }
    //---
    return [
        "test" => !empty($test),
        "doit" => $doit,
        "code" => $code,
        "cat" => $cat,
        "camp" => $camp,
        "tra_type" => $tra_type,
        "filter_sparql" => $filter_sparql,
        "code_lang_name" => $code_lang_name,
        "errors" => $errors,
    ];
}
