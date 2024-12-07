<?php

namespace SQLorAPI\GetDataTab;

/*

Usage:

use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;
use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function SQLorAPI\GetDataTab\get_td_or_sql_categories;
use function SQLorAPI\GetDataTab\get_td_or_sql_projects;
use function SQLorAPI\GetDataTab\get_td_or_sql_settings;
use function SQLorAPI\GetDataTab\get_td_or_sql_views;

*/

use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;
use function SQLorAPI\Get\isvalid;

function get_td_or_sql_views()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'views']);
    } else {
        $query = "SELECT target, lang, countall, count2021, count2022, count2023, count2024, count2025, count2026 FROM views";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_td_or_sql_settings()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'settings']);
    } else {
        $query = "select id, title, displayed, value, Type from settings";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_td_or_sql_projects()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'projects']);
    } else {
        $query = "select g_id, g_title from projects";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_td_or_sql_categories()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'categories']);
    } else {
        $query = "select id, category, category2, campaign, depth, def from categories";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_td_or_sql_qids()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'qids']);
    } else {
        $query = "SELECT title, qid FROM qids";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_td_or_sql_full_translators()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'full_translators']);
    } else {
        $query = "SELECT * FROM full_translators";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_td_or_sql_translate_type()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'translate_type']);
    } else {
        $query = "SELECT tt_title, tt_lead, tt_full FROM translate_type";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_user_pages($user_main, $year_y, $lang_y)
{
    // ---
    global $from_api;
    // ---
    $api_params = ['get' => 'pages', 'user' => $user_main];
    // ---
    $query = "select * from pages where user = ?";
    $sql_params = [$user_main];
    // ---
    if (isvalid($year_y)) {
        $query .= " and YEAR(date) = ?";
        $sql_params[] = $year_y;
        // ---
        $api_params['YEAR(date)'] = $year_y;
    };
    // ---
    if (isvalid($lang_y)) {
        $query .= " and lang = ?";
        $sql_params[] = $lang_y;
        // ---
        $api_params['lang'] = $lang_y;
    };
    //---
    if ($from_api) {
        $data = get_td_api($api_params);
    } else {
        $data = fetch_query($query, $sql_params);
    }
    // ---
    return $data;
}

function get_user_views($user, $year_y, $lang_y)
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'user_views', 'user' => $user]);
    } else {
        $query = "select p.target, v.countall from pages p, views v where p.user = ? and p.lang = v.lang and p.target = v.target";
        $params = [$user];
        $data = fetch_query($query, $params);
    }
    // ---
    return $data;
}


function get_pages_with_pupdate()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(pupdate) AS year', 'pupdate' => 'not_empty']);
    } else {
        $query = "SELECT DISTINCT YEAR(pupdate) AS year FROM pages WHERE pupdate <> ''";
        $data = fetch_query($query);
    }
    // ---
    return array_map('current', $data);
}


function get_graph_data()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'graph_data']);
    } else {
        $query = <<<SQL
            SELECT LEFT(pupdate, 7) as m, COUNT(*) as c
            FROM pages
            WHERE target != ''
            GROUP BY LEFT(pupdate, 7)
            ORDER BY LEFT(pupdate, 7) ASC;
        SQL;
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_lang_pages($lang, $year_y)
{
    // ---
    global $from_api;
    // ---
    $api_params = ['get' => 'pages', 'lang' => $lang];
    // ---
    $query = "select target, lang, title, date, pupdate from pages where lang = ?";
    $params = [$lang];
    // ---
    if (isvalid($year_y)) {
        $query .= " and YEAR(date) = ?";
        $params[] = $year_y;
        // ---
        $api_params['YEAR(date)'] = $year_y;
    };
    // ---
    if ($from_api) {
        $data = get_td_api($api_params);
    } else {
        $data = fetch_query($query, $params);
    }
    // ---
    return $data;
}
function get_lang_views($mainlang)
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'lang_views', 'lang' => $mainlang]);
    } else {
        $query = "select p.target, v.countall from pages p, views v where p.lang = ? and p.lang = v.lang and p.target = v.target";
        $params = [$mainlang];
        $data = fetch_query($query, $params);
    }
    // ---
    return $data;
}

function get_lang_years($mainlang)
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(pupdate) AS year', 'pupdate' => 'not_empty', 'lang' => $mainlang]);
    } else {
        $query = "SELECT DISTINCT YEAR(pupdate) AS year FROM pages WHERE lang = ? AND pupdate <> ''";
        $params = [$mainlang];
        $data = fetch_query($query, $params);
    }
    // ---
    return array_map('current', $data);
}

function get_user_years($user)
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(date) AS year', 'user' => $user]);
    } else {
        $query = "SELECT DISTINCT YEAR(date) AS year FROM pages WHERE user = ?";
        $params = [$user];
        $data = fetch_query($query, $params);
    }
    // ---
    return array_map('current', $data);
}

function get_user_langs($user)
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'pages', 'distinct' => "1", 'select' => 'lang', 'user' => $user]);
    } else {
        $query = "SELECT DISTINCT lang FROM pages WHERE user = ?";
        $params = [$user];
        $data = fetch_query($query, $params);
    }
    // ---
    return array_map('current', $data);
}
