<?php

namespace Leaderboard\Get;

/*

Usage:

use function Leaderboard\Get\get_lang_years;
use function Leaderboard\Get\get_user_years;
use function Leaderboard\Get\get_user_langs;
use function Leaderboard\Get\get_lang_views;
use function Leaderboard\Get\get_lang_pages;
use function Leaderboard\Get\get_graph_data;

*/

use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;

$from_api  = (isset($_GET['from_api'])) ? true : false;

function gerr($mainlang)
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api();
    } else {
        $query = "";
        $params = [];
        $data = fetch_query($query, $params);
    }
    // ---
    return $data;
}
function gerr1($mainlang)
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api();
    } else {
        $query = "";
        $params = [];
        $data = fetch_query($query, $params);
    }
    // ---
    return $data;
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
    if ($year_y != 'All' && !empty($year_y)) {
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
