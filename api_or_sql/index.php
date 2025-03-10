<?php

namespace SQLorAPI\Get;

/*

Usage:

use function SQLorAPI\Get\isvalid;
use function SQLorAPI\Get\get_lang_years;
use function SQLorAPI\Get\get_user_years;
use function SQLorAPI\Get\get_user_langs;
use function SQLorAPI\Get\get_lang_views;
use function SQLorAPI\Get\get_lang_pages;
use function SQLorAPI\Get\get_graph_data;
use function SQLorAPI\Get\get_pages_with_pupdate;
use function SQLorAPI\Get\get_user_views;
use function SQLorAPI\Get\get_user_pages;
use function SQLorAPI\Get\get_coordinator;
use function SQLorAPI\Get\get_in_process_tdapi;

*/

use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;

$settings_tabe = array_column(get_td_api(['get' => 'settings']), 'value', 'title');
//---
$from_api  = (($settings_tabe['use_td_api'] ?? "") == "1") ? true : false;

include_once __DIR__ . '/get_lead.php';
include_once __DIR__ . '/data_tab.php';

$data_index = [];

function isvalid($str)
{
    return !empty($str) && $str != 'All' && $str != 'all';
}

function get_in_process_tdapi($code)
{
    // ---
    global $from_api, $data_index;
    // ---
    if (!empty($data_index['in_process_tdapi' . $code] ?? [])) {
        return $data_index['in_process_tdapi' . $code];
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'pages', 'lang' => $code, 'target' => 'empty']);
    } else {
        $query = "select * from pages where target = '' and lang = ?;";
        $params = [$code];
        $data = fetch_query($query, $params);
    }
    // ---
    $data_index['in_process_tdapi' . $code] = $data;
    // ---
    return $data;
}
function get_coordinator()
{
    // ---
    global $from_api, $data_index;
    // ---
    if (!empty($data_index['coordinator'] ?? [])) {
        return $data_index['coordinator'];
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'coordinator', 'select' => 'user']);
    } else {
        $query = "SELECT user FROM coordinator;";
        //---
        $data = fetch_query($query);
    }
    // ---
    $data_index['coordinator'] = $data;
    // ---
    return $data;
}
function get_user_pages($user_main, $year_y, $lang_y)
{
    // ---
    global $from_api, $data_index;
    // ---
    $key = 'user_pages_' . $user_main . '_' . $year_y . '_' . $lang_y;
    // ---
    if (!empty($data_index[$key] ?? [])) {
        return $data_index[$key];
    }
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
        $api_params['year'] = $year_y;
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
    $data_index[$key] = $data;
    // ---
    return $data;
}

function get_user_views($user, $year_y, $lang_y)
{
    // ---
    global $from_api, $data_index;
    // ---
    $key = 'user_views_' . $user . '_' . $year_y . '_' . $lang_y;
    // ---
    if (!empty($data_index[$key] ?? [])) {
        return $data_index[$key];
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'user_views', 'user' => $user, 'year' => $year_y, 'lang' => $lang_y]);
    } else {
        $query = "select p.target, v.countall from pages p, views v where p.user = ? and p.lang = v.lang and p.target = v.target";
        $params = [$user];
        $data = fetch_query($query, $params);
    }
    // ---
    $data_index[$key] = $data;
    // ---
    return $data;
}


function get_pages_with_pupdate()
{
    // ---
    global $from_api, $data_index;
    // ---
    if (!empty($data_index['pages_with_pupdate'] ?? [])) {
        return $data_index['pages_with_pupdate'];
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(pupdate) AS year', 'pupdate' => 'not_empty']);
    } else {
        $query = "SELECT DISTINCT YEAR(pupdate) AS year FROM pages WHERE pupdate <> ''";
        $data = fetch_query($query);
    }
    // ---
    $data = array_map('current', $data);
    // ---
    $data_index['pages_with_pupdate'] = $data;
    // ---
    return $data;
}


function get_graph_data()
{
    // ---
    global $from_api, $data_index;
    // ---
    if (!empty($data_index['graph_data'] ?? [])) {
        return $data_index['graph_data'];
    }
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
    $data_index['graph_data'] = $data;
    // ---
    return $data;
}
function get_lang_pages($lang, $year_y)
{
    // ---
    global $from_api, $data_index;
    // ---
    if (!empty($data_index['lang_pages' . $lang . $year_y] ?? [])) {
        return $data_index['lang_pages' . $lang . $year_y];
    }
    // ---
    $api_params = ['get' => 'pages', 'lang' => $lang];
    // ---
    $query = "select target, user, lang, title, date, pupdate from pages where lang = ?";
    $params = [$lang];
    // ---
    if (isvalid($year_y)) {
        $query .= " and YEAR(date) = ?";
        $params[] = $year_y;
        // ---
        $api_params['year'] = $year_y;
    };
    // ---
    if ($from_api) {
        $data = get_td_api($api_params);
    } else {
        $data = fetch_query($query, $params);
    }
    // ---
    $data_index['lang_pages' . $lang . $year_y] = $data;
    // ---
    return $data;
}
function get_lang_views($mainlang, $year_y)
{
    // ---
    global $from_api, $data_index;
    // ---
    if (!empty($data_index['lang_views' . $mainlang . $year_y] ?? [])) {
        return $data_index['lang_views' . $mainlang . $year_y];
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'lang_views', 'lang' => $mainlang, 'year' => $year_y]);
    } else {
        $query = <<<SQL
            select p.target, v.countall
            from pages p, views v
            WHERE p.target = v.target
            and p.lang = v.lang
            and p.lang = ?
        SQL;
        // ---
        $sql_params = [$mainlang];
        // ---
        if (isvalid($year_y)) {
            $query .= " and YEAR(pupdate) = ?";
            $sql_params[] = $year_y;
        };
        // ---
        $data = fetch_query($query, $sql_params);
    }
    // ---
    $data_index['lang_views' . $mainlang . $year_y] = $data;
    // ---
    return $data;
}

function get_lang_years($mainlang)
{
    // ---
    global $from_api, $data_index;
    // ---
    if (!empty($data_index['lang_years' . $mainlang] ?? [])) {
        return $data_index['lang_years' . $mainlang];
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(pupdate) AS year', 'pupdate' => 'not_empty', 'lang' => $mainlang]);
    } else {
        $query = "SELECT DISTINCT YEAR(pupdate) AS year FROM pages WHERE lang = ? AND pupdate <> ''";
        $params = [$mainlang];
        $data = fetch_query($query, $params);
    }
    // ---
    $data = array_map('current', $data);
    // ---
    $data_index['lang_years' . $mainlang] = $data;
    // ---
    return $data;
}

function get_user_years($user)
{
    // ---
    global $from_api, $data_index;
    // ---
    if (!empty($data_index['user_years' . $user] ?? [])) {
        return $data_index['user_years' . $user];
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(date) AS year', 'user' => $user]);
    } else {
        $query = "SELECT DISTINCT YEAR(date) AS year FROM pages WHERE user = ?";
        $params = [$user];
        $data = fetch_query($query, $params);
    }
    // ---
    $data = array_map('current', $data);
    // ---
    $data_index['user_years' . $user] = $data;
    // ---
    return $data;
}

function get_user_langs($user)
{
    // ---
    global $from_api, $data_index;
    // ---
    if (!empty($data_index['user_langs' . $user] ?? [])) {
        return $data_index['user_langs' . $user];
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'pages', 'distinct' => "1", 'select' => 'lang', 'user' => $user]);
    } else {
        $query = "SELECT DISTINCT lang FROM pages WHERE user = ?";
        $params = [$user];
        $data = fetch_query($query, $params);
    }
    // ---
    $data = array_map('current', $data);
    // ---
    $data_index['user_langs' . $user] = $data;
    // ---
    return $data;
}
