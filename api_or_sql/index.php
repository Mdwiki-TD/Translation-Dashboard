<?php

namespace SQLorAPI\Get;

/*

Usage:

use function SQLorAPI\Get\super_function;
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
use function SQLorAPI\Get\get_inprocess_lang_new;
use function SQLorAPI\Get\get_inprocess_user_new;

*/

use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;

$settings_tabe = array_column(get_td_api(['get' => 'settings']), 'value', 'title');
//---
$use_td_api  = (($settings_tabe['use_td_api'] ?? "") == "1") ? true : false;

if (isset($_GET['use_td_api'])) {
    $use_td_api  = $_GET['use_td_api'] != "x";
}

include_once __DIR__ . '/get_lead.php';
include_once __DIR__ . '/data_tab.php';

$data_index = [];

function isvalid($str)
{
    return !empty($str) && $str != 'All' && $str != 'all';
}

function super_function($api_params, $sql_params, $sql_query)
{
    global $use_td_api;
    // ---
    if ($use_td_api) {
        $data = get_td_api($api_params);
    } else {
        $data = fetch_query($sql_query, $sql_params);
    }
    // ---
    return $data;
}

function get_inprocess_user_new($user)
{
    // ---
    global $data_index;
    // ---
    if (!empty($data_index['inprocess_tdapi' . $user] ?? [])) {
        return $data_index['inprocess_tdapi' . $user];
    }
    // ---
    $api_params = ['get' => 'in_process', 'user' => $user];
    $query = "select * from in_process where user = ?";
    $params = [$user];
    $data = super_function($api_params, $params, $query);
    // ---
    $data_index['inprocess_tdapi' . $user] = $data;
    // ---
    return $data;
}

function get_inprocess_lang_new($code)
{
    // ---
    global $data_index;
    // ---
    if (!empty($data_index['inprocess_tdapi' . $code] ?? [])) {
        return $data_index['inprocess_tdapi' . $code];
    }
    // ---
    /*
    SELECT * from in_process ip
        WHERE NOT EXISTS (
        SELECT p.user FROM pages p
        where p.title = ip.title
        and p.lang = ip.lang
        and p.target != ""
        )
    */
    // ---
    $api_params = ['get' => 'in_process', 'lang' => $code];
    $query = "select * from in_process where lang = ?";
    $params = [$code];
    $data = super_function($api_params, $params, $query);
    // ---
    $data_index['inprocess_tdapi' . $code] = $data;
    // ---
    return $data;
}

function get_coordinator()
{
    // ---
    static $coordinator = [];
    // ---
    if (!empty($coordinator ?? [])) {
        return $coordinator;
    }
    // ---
    $api_params = ['get' => 'coordinator', 'select' => 'user'];
    $query = "SELECT user FROM coordinator;";
    $data = super_function($api_params, [], $query);
    // ---
    $coordinator = $data;
    // ---
    return $data;
}
function get_user_pages($user_main, $year_y, $lang_y)
{
    // ---
    global $data_index;
    // ---
    $key = 'user_pages_' . $user_main . '_' . $year_y . '_' . $lang_y;
    // ---
    if (!empty($data_index[$key] ?? [])) {
        return $data_index[$key];
    }
    // ---
    $api_params = ['get' => 'pages_by_user_or_lang', 'user' => $user_main];
    // ---
    $query = "select * from pages_by_user_or_lang where user = ?";
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
    $data = super_function($api_params, $sql_params, $query);
    // ---
    $data_index[$key] = $data;
    // ---
    return $data;
}

function get_pages_with_pupdate()
{
    // ---
    static $pages_with_pupdate = [];
    // ---
    if (!empty($pages_with_pupdate ?? [])) {
        return $pages_with_pupdate;
    }
    // ---
    $api_params = ['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(pupdate) AS year', 'pupdate' => 'not_empty'];
    $query = "SELECT DISTINCT YEAR(pupdate) AS year FROM pages WHERE pupdate <> ''";
    $data = super_function($api_params, [], $query);
    // ---
    $data = array_map('current', $data);
    // ---
    $pages_with_pupdate = $data;
    // ---
    return $data;
}


function get_graph_data()
{
    // ---
    static $graph_data = [];
    // ---
    if (!empty($graph_data ?? [])) {
        return $graph_data;
    }
    // ---
    $api_params = ['get' => 'graph_data'];
    $query = <<<SQL
        SELECT LEFT(pupdate, 7) as m, COUNT(*) as c
        FROM pages
        WHERE target != ''
        GROUP BY LEFT(pupdate, 7)
        ORDER BY LEFT(pupdate, 7) ASC;
    SQL;
    $data = super_function($api_params, [], $query);
    // ---
    $graph_data = $data;
    // ---
    return $data;
}
function get_lang_pages($lang, $year_y)
{
    // ---
    global $data_index;
    // ---
    if (!empty($data_index['lang_pages' . $lang . $year_y] ?? [])) {
        return $data_index['lang_pages' . $lang . $year_y];
    }
    // ---
    $api_params = ['get' => 'pages_by_user_or_lang', 'lang' => $lang];
    // ---
    $query = "select * from pages where lang = ?";
    $params = [$lang];
    // ---
    if (isvalid($year_y)) {
        $query .= " and YEAR(date) = ?";
        $params[] = $year_y;
        // ---
        $api_params['year'] = $year_y;
    };
    // ---
    $data = super_function($api_params, $params, $query);
    // ---
    $data_index['lang_pages' . $lang . $year_y] = $data;
    // ---
    return $data;
}

function get_user_views($user, $year_y, $lang_y)
{
    // ---
    global $data_index;
    // ---
    $key = 'user_views_' . $user . '_' . $year_y . '_' . $lang_y;
    // ---
    if (!empty($data_index[$key] ?? [])) {
        return $data_index[$key];
    }
    // ---
    $api_params = ['get' => 'user_views2', 'lang' => $lang_y, 'user' => $user, 'year' => $year_y];
    // ---
    $query2 = <<<SQL
        SELECT v.target, v.lang, v.views
        FROM views_new_all v
        JOIN pages p
            ON p.target = v.target
            AND p.lang = v.lang
        WHERE p.user = ?
    SQL;
    // ---
    $sql_params = [$user];
    // ---
    if (isvalid($year_y)) {
        $query2 .= " and YEAR(p.pupdate) = ?";
        $sql_params[] = $year_y;
    }
    // ---
    $data = super_function($api_params, $sql_params, $query2);
    // ---
    $table_of_views = [];
    // ---
    foreach ($data as $Key => $table) {
        $targ = $table['target'] ?? "";
        $lang = $table['lang'] ?? "";
        // ---
        if (!array_key_exists($lang, $table_of_views)) {
            $table_of_views[$lang] = [];
        };
        // ---
        $views = isset($table['views']) ? $table['views'] : 0;
        // ---
        $table_of_views[$lang][$targ] = $views;
    };
    // ---
    $data_index[$key] = $data;
    // ---
    return $table_of_views;
}

function get_lang_views($mainlang, $year_y)
{
    // ---
    global $data_index;
    // ---
    $key = 'lang_views_' . $mainlang . '_' . $year_y;
    // ---
    if (!empty($data_index[$key] ?? [])) {
        return $data_index[$key];
    }
    // ---
    $api_params = ['get' => 'lang_views2', 'lang' => $mainlang, 'year' => $year_y];
    // ---
    $query2 = <<<SQL
        SELECT v.target, v.lang, v.views
        FROM views_new_all v
        JOIN pages p
            ON p.target = v.target
            AND p.lang = v.lang
        WHERE p.lang = ?
    SQL;
    // ---
    $sql_params = [$mainlang];
    // ---
    if (isvalid($year_y)) {
        $query2 .= " and YEAR(p.pupdate) = ?";
        $sql_params[] = $year_y;
    };
    // ---
    $data = super_function($api_params, $sql_params, $query2);
    // ---
    $table_of_views = [];
    // ---
    foreach ($data as $Key => $table) {
        $targ = $table['target'] ?? "";
        // ---
        $views = isset($table['views']) ? $table['views'] : 0;
        // ---
        $table_of_views[$targ] = $views;
    };
    //---
    $data_index[$key] = $table_of_views;
    // ---
    return $table_of_views;
}

function get_lang_years($mainlang)
{
    // ---
    global $data_index;
    // ---
    if (!empty($data_index['lang_years' . $mainlang] ?? [])) {
        return $data_index['lang_years' . $mainlang];
    }
    // ---
    $api_params = ['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(pupdate) AS year', 'pupdate' => 'not_empty', 'lang' => $mainlang];
    $query = "SELECT DISTINCT YEAR(pupdate) AS year FROM pages WHERE lang = ? AND pupdate <> ''";
    $params = [$mainlang];
    $data = super_function($api_params, $params, $query);
    // ---
    $data = array_map('current', $data);
    // ---
    // sort years
    rsort($data);
    // ---
    $data_index['lang_years' . $mainlang] = $data;
    // ---
    return $data;
}

function get_user_years($user)
{
    // ---
    global $data_index;
    // ---
    if (!empty($data_index['user_years' . $user] ?? [])) {
        return $data_index['user_years' . $user];
    }
    // ---
    $api_params = ['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(date) AS year', 'user' => $user];
    $query = "SELECT DISTINCT YEAR(date) AS year FROM pages WHERE user = ?";
    // ---
    $params = [$user];
    // ---
    $data = super_function($api_params, $params, $query);
    // ---
    $data = array_map('current', $data);
    // ---
    // sort years
    rsort($data);
    // ---
    $data_index['user_years' . $user] = $data;
    // ---
    return $data;
}

function get_user_langs($user)
{
    // ---
    global $data_index;
    // ---
    if (!empty($data_index['user_langs' . $user] ?? [])) {
        return $data_index['user_langs' . $user];
    }
    // ---
    $api_params = ['get' => 'pages', 'distinct' => "1", 'select' => 'lang', 'user' => $user];
    $query = "SELECT DISTINCT lang FROM pages WHERE user = ?";
    $params = [$user];
    $data = super_function($api_params, $params, $query);
    // ---
    $data = array_map('current', $data);
    // ---
    $data_index['user_langs' . $user] = $data;
    // ---
    return $data;
}
