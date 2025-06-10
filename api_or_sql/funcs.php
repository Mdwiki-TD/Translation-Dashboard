<?php

namespace SQLorAPI\Funcs;

/*

Usage:

use function SQLorAPI\Funcs\get_lang_years;
use function SQLorAPI\Funcs\get_user_years;
use function SQLorAPI\Funcs\get_user_langs;
use function SQLorAPI\Funcs\get_lang_views;
use function SQLorAPI\Funcs\get_lang_pages;
use function SQLorAPI\Funcs\get_graph_data;
use function SQLorAPI\Funcs\get_pages_with_pupdate;
use function SQLorAPI\Funcs\get_user_views;
use function SQLorAPI\Funcs\get_user_pages;
use function SQLorAPI\Funcs\get_coordinator;

*/

use function SQLorAPI\Get\super_function;
use function SQLorAPI\Get\isvalid;

function get_coordinator()
{
    // ---
    static $coordinator = [];
    // ---
    if (!empty($coordinator ?? [])) {
        return $coordinator;
    }
    // ---
    $api_params = ['get' => 'coordinator'];
    $query = "SELECT id, user FROM coordinator order by id";
    //---
    $data = super_function($api_params, [], $query);
    // ---
    $coordinator = $data;
    // ---
    return $data;
}

function get_user_pages($user_main, $year_y, $lang_y)
{
    // ---
    static $data = [];
    // ---
    $key = $user_main . '_' . $year_y . '_' . $lang_y;
    // ---
    if (!empty($data[$key] ?? [])) {
        return $data[$key];
    }
    // ---
    $api_params = ['get' => 'pages_by_user_or_lang', 'user' => $user_main];
    // ---
    $query = <<<SQL
        SELECT DISTINCT p.title, p.word, p.translate_type, p.cat, p.lang, p.user, p.target, p.date,
        p.pupdate, p.add_date, p.deleted, v.views
        FROM pages p
        LEFT JOIN views_new_all v
            ON p.target = v.target
            AND p.lang = v.lang
        where user = ?
    SQL;
    // ---
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
    $data[$key] = $data;
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
    static $data = [];
    // ---
    if (!empty($data[$lang . $year_y] ?? [])) {
        return $data[$lang . $year_y];
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
    $data[$lang . $year_y] = $data;
    // ---
    return $data;
}

function get_user_views($user, $year_y, $lang_y)
{
    // ---
    static $data = [];
    // ---
    $key = 'user_views_' . $user . '_' . $year_y . '_' . $lang_y;
    // ---
    if (!empty($data[$key] ?? [])) {
        return $data[$key];
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
    $data[$key] = $data;
    // ---
    return $table_of_views;
}

function get_lang_views($mainlang, $year_y)
{
    // ---
    static $data = [];
    // ---
    $key = 'lang_views_' . $mainlang . '_' . $year_y;
    // ---
    if (!empty($data[$key] ?? [])) {
        return $data[$key];
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
    $data[$key] = $table_of_views;
    // ---
    return $table_of_views;
}

function get_lang_years($mainlang)
{
    // ---
    static $data = [];
    // ---
    if (!empty($data[$mainlang] ?? [])) {
        return $data[$mainlang];
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
    $data[$mainlang] = $data;
    // ---
    return $data;
}

function get_user_years($user)
{
    // ---
    static $data = [];
    // ---
    if (!empty($data[$user] ?? [])) {
        return $data[$user];
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
    $data[$user] = $data;
    // ---
    return $data;
}

function get_user_langs($user)
{
    // ---
    static $data = [];
    // ---
    if (!empty($data[$user] ?? [])) {
        return $data[$user];
    }
    // ---
    $api_params = ['get' => 'pages', 'distinct' => "1", 'select' => 'lang', 'user' => $user];
    $query = "SELECT DISTINCT lang FROM pages WHERE user = ?";
    $params = [$user];
    $data = super_function($api_params, $params, $query);
    // ---
    $data = array_map('current', $data);
    // ---
    $data[$user] = $data;
    // ---
    return $data;
}
