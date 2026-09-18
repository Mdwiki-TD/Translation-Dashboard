<?php

namespace SQLorAPI\Process;

/*

Usage:

use function SQLorAPI\Process\get_process_data;
use function SQLorAPI\Process\get_user_process_new;
use function SQLorAPI\Process\get_users_process_new;
use function SQLorAPI\Process\get_lang_in_process_by_cat;
use function SQLorAPI\Process\get_lang_in_process_by_year;
*/

use function SQLorAPI\Get\super_function;
use function SQLorAPI\Get\isvalid;

function get_process_data(): array
{
    // ---
    static $process_all = [];
    // ---
    if (!empty($process_all)) {
        return $process_all;
    }
    // ---
    $api_params = ['get' => 'in_process', 'limit' => "100", "order" => 'add_date'];
    $sql_t = "select * from in_process ORDER BY add_date DESC limit 100";
    //---
    $process_all = super_function($api_params, [], $sql_t);
    // ---
    return $process_all;
}

function get_user_process_new(string $user, string $year_y = "all")
{
    // ---
    static $cache = [];
    // ---
    if (!empty($cache[$user] ?? [])) {
        return $cache[$user];
    }
    // ---
    $api_params = ['get' => 'in_process', 'user' => $user];
    // ---
    $query = "select * from in_process where user = ?";
    // ---
    $params = [$user];
    // ---
    if (isvalid($year_y)) {
        $query .= " AND YEAR(add_date) = ?";
        $params[] = $year_y;
        $api_params['year'] = $year_y;
    }
    // ---
    $data = super_function($api_params, $params, $query, "in_process", true);
    // ---
    $cache[$user] = $data;
    // ---
    return $data;
}

function get_users_process_new(): array
{
    // ---
    static $process_new = [];
    // ---
    if (!empty($process_new)) {
        return $process_new;
    }
    // ---
    // ttp://localhost:9002/api.php?get=in_process&distinct=true&limit=50&group=user&order=count&select=count
    // ---
    $api_params = ['get' => 'in_process', 'distinct' => 'true', "select" => 'user', 'group' => 'user', "order" => '2', "count" => '*'];
    // ---
    $sql_t = 'select DISTINCT user, count(*) as count from in_process group by user order by count desc';
    // ---
    $tab = super_function($api_params, [], $sql_t);
    // ---
    $process_new = array_column($tab, 'count', 'user');
    //---
    return $process_new;
}

function get_lang_in_process_by_cat($code, $category): array
{
    // ---
    static $cache = [];
    // ---
    if (!isvalid($category)) {
        return [];
    }
    // ---
    if (!empty($cache[$code][$category] ?? [])) return $cache[$code][$category];
    // ---
    $query = "select * from in_process where lang = ? AND cat = ?";
    // ---
    $api_params = ['get' => 'in_process', 'lang' => $code, 'cat' => $category];
    // ---
    $params = [$code, $category];
    // ---
    $data = super_function($api_params, $params, $query, "in_process");
    // ---
    $cache[$code][$category] = $data;
    //---
    return $cache[$code][$category];
}

function get_lang_in_process_by_year($code, $year_y = "all"): array
{
    // ---
    static $cache = [];
    // ---
    if (!empty($cache[$code][$year_y] ?? [])) return $cache[$code][$year_y];
    // ---
    $query = "select * from in_process where lang = ?";
    // ---
    $api_params = ['get' => 'in_process', 'lang' => $code];
    // ---
    $params = [$code];
    // ---
    if (isvalid($year_y)) {
        $query .= " AND YEAR(add_date) = ?";
        $params[] = $year_y;
        $api_params['year'] = $year_y;
    }
    // ---
    $data = super_function($api_params, $params, $query, "in_process");
    // ---
    $cache[$code][$year_y] = $data;
    //---
    return $cache[$code][$year_y];
}

function get_lang_in_process($code): array
{
    // ---
    static $cache = [];
    // ---
    if (!empty($cache[$code] ?? [])) return $cache[$code];
    // ---
    $query = "select * from in_process where lang = ?";
    // ---
    $api_params = ['get' => 'in_process', 'lang' => $code];
    // ---
    $params = [$code];
    // ---
    $data = super_function($api_params, $params, $query, "in_process");
    // ---
    $cache[$code] = $data;
    //---
    return $data;
}
