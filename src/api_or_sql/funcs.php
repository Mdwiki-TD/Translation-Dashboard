<?php

namespace SQLorAPI\Funcs;

/*

Usage:

use function SQLorAPI\Funcs\get_lang_years;
use function SQLorAPI\Funcs\get_user_years;
use function SQLorAPI\Funcs\get_user_langs;
use function SQLorAPI\Funcs\get_user_camps;
use function SQLorAPI\Funcs\get_lang_views;
use function SQLorAPI\Funcs\get_lang_pages;
use function SQLorAPI\Funcs\get_graph_data;
use function SQLorAPI\Funcs\get_pages_with_pupdate;
use function SQLorAPI\Funcs\get_user_views;
use function SQLorAPI\Funcs\get_user_pages;
use function SQLorAPI\Funcs\get_coordinator;
use function SQLorAPI\Funcs\get_lang_pages_by_cat;

*/

use function SQLorAPI\Get\super_function;
use function SQLorAPI\Get\isvalid;

function get_lang_pages_by_cat($lang, $cat)
{
    // ---
    // http://localhost:9001/api.php?get=pages&lang=ar&cat=RTT
    static $data = [];
    // ---
    if (!empty($data[$lang . $cat] ?? [])) {
        return $data[$lang . $cat];
    }
    // ---
    $api_params = ['get' => 'pages', 'lang' => $lang, 'cat' => $cat];
    // ---
    $query = "select * from pages p where p.lang = ? and p.cat = ?";
    $params = [$lang, $cat];
    // ---
    $u_data = super_function($api_params, $params, $query);
    // ---
    $data[$lang . $cat] = $u_data;
    // ---
    return $u_data;
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
    $api_params = ['get' => 'coordinator'];
    $query = "SELECT id, user FROM coordinator order by id";
    //---
    $u_data = super_function($api_params, [], $query);
    // ---
    $coordinator = $u_data;
    // ---
    return $u_data;
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
        where p.user = ?
    SQL;
    // ---
    $sql_params = [$user_main];
    // ---
    if (isvalid($year_y)) {
        $query .= " and YEAR(p.date) = ?";
        $sql_params[] = $year_y;
        // ---
        $api_params['year'] = $year_y;
    };
    // ---
    if (isvalid($lang_y)) {
        $query .= " and p.lang = ?";
        $sql_params[] = $lang_y;
        // ---
        $api_params['lang'] = $lang_y;
    };
    //---
    $u_data = super_function($api_params, $sql_params, $query);
    // ---
    $data[$key] = $u_data;
    // ---
    return $u_data;
}

function get_pages_with_pupdate()
{
    // ---
    static $data = [];
    // ---
    if (!empty($data ?? [])) {
        return $data;
    }
    // ---
    $api_params = ['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(pupdate) AS year', 'pupdate' => 'not_empty'];
    // ---
    $query = "SELECT DISTINCT YEAR(pupdate) AS year FROM pages WHERE pupdate <> ''";
    // ---
    $u_data = super_function($api_params, [], $query);
    // ---
    $u_data = array_map('current', $u_data);
    // ---
    $data = $u_data;
    // ---
    return $u_data;
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
    $u_data = super_function($api_params, [], $query);
    // ---
    $graph_data = $u_data;
    // ---
    return $u_data;
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
    $query = "select * from pages p where p.lang = ?";
    $params = [$lang];
    // ---
    if (isvalid($year_y)) {
        $query .= " and YEAR(p.date) = ?";
        $params[] = $year_y;
        // ---
        $api_params['year'] = $year_y;
    };
    // ---
    $u_data = super_function($api_params, $params, $query);
    // ---
    $data[$lang . $year_y] = $u_data;
    // ---
    return $u_data;
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
    $u_data = super_function($api_params, $sql_params, $query2);
    // ---
    $table_of_views = [];
    // ---
    foreach ($u_data as $Key => $table) {
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
    $data[$key] = $table_of_views;
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
    $u_data = super_function($api_params, $sql_params, $query2);
    // ---
    $table_of_views = [];
    // ---
    foreach ($u_data as $Key => $table) {
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
    // $api_params = ['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(pupdate) AS year', 'pupdate' => 'not_empty', 'lang' => $mainlang];
    $api_params = ['get' => 'user_lang_status', 'select' => 'year', 'lang' => $mainlang];
    // ---
    $query = "SELECT DISTINCT YEAR(p.pupdate) AS year FROM pages p WHERE p.lang = ?";
    $params = [$mainlang];
    // ---
    $u_data = super_function($api_params, $params, $query);
    // ---
    $u_data = array_map('current', $u_data);
    // ---
    // sort years
    rsort($u_data);
    // ---
    $data[$mainlang] = $u_data;
    // ---
    return $u_data;
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
    // $api_params = ['get' => 'pages', 'distinct' => "1", 'select' => 'YEAR(date) AS year', 'user' => $user];
    $api_params = ['get' => 'user_status', 'select' => 'year', 'user' => $user];
    // ---
    $query = "SELECT DISTINCT YEAR(p.date) AS year FROM pages p WHERE p.user = ?";
    // ---
    $params = [$user];
    // ---
    $u_data = super_function($api_params, $params, $query);
    // ---
    $u_data = array_map('current', $u_data);
    // ---
    // remove empty or null years
    $u_data = array_filter($u_data, function ($value) {
        return !empty($value);
    });
    // ---
    // sort years
    rsort($u_data);
    // ---
    $data[$user] = $u_data;
    // ---
    return $u_data;
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
    // $api_params = ['get' => 'pages', 'distinct' => "1", 'select' => 'lang', 'user' => $user];
    $api_params = ['get' => 'user_status', 'select' => 'lang', 'user' => $user];
    // ---
    $query = "SELECT DISTINCT p.lang FROM pages p WHERE p.user = ?";
    $params = [$user];
    // ---
    $u_data = super_function($api_params, $params, $query);
    // ---
    $u_data = array_map('current', $u_data);
    // ---
    // remove empty or null years
    $u_data = array_filter($u_data, function ($value) {
        return !empty($value);
    });
    // ---
    $data[$user] = $u_data;
    // ---
    return $u_data;
}


function get_user_camps($user)
{
    // ---
    static $data = [];
    // ---
    if (!empty($data[$user] ?? [])) {
        return $data[$user];
    }
    // ---
    $api_params = ['get' => 'user_status', 'select' => 'campaign', 'user' => $user];
    // ---
    $query = "SELECT DISTINCT ca.campaign
        FROM pages p
        LEFT JOIN categories ca
        ON p.cat = ca.category
        WHERE p.user = ?
    ";
    $params = [$user];
    // ---
    $u_data = super_function($api_params, $params, $query);
    // ---
    $u_data = array_map('current', $u_data);
    // ---
    // remove empty or null years
    $u_data = array_filter($u_data, function ($value) {
        return !empty($value);
    });
    // ---
    $data[$user] = $u_data;
    // ---
    return $u_data;
}
