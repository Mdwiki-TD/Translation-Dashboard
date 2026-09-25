<?php

namespace App\SQLorAPI\Process;

use function App\SQLorAPI\Get\super_function;
use function App\SQLorAPI\Get\isvalid;

function get_process_data(): array
{

    static $process_all = [];

    if (!empty($process_all)) {
        return $process_all;
    }

    $apiParams = ['get' => 'in_process', 'limit' => "100", "order" => 'add_date'];
    $sql_t = "select * from in_process ORDER BY add_date DESC limit 100";

    $process_all = super_function($apiParams, [], $sql_t);

    return $process_all;
}

function get_user_process_new(string $user, string $year_y = "all")
{

    static $cache = [];

    if (!empty($cache[$user] ?? [])) {
        return $cache[$user];
    }

    $apiParams = ['get' => 'in_process', 'user' => $user];

    $query = "select * from in_process where user = ?";

    $params = [$user];

    if (isvalid($year_y)) {
        $query .= " AND YEAR(add_date) = ?";
        $params[] = $year_y;
        $apiParams['year'] = $year_y;
    }

    $data = super_function($apiParams, $params, $query, true);

    $cache[$user] = $data;

    return $data;
}

function get_users_process_new(): array
{

    static $processNew = [];

    if (!empty($processNew)) {
        return $processNew;
    }

    // ttp://localhost:9002/api.php?get=in_process&distinct=true&limit=50&group=user&order=count&select=count

    $apiParams = ['get' => 'in_process', 'distinct' => 'true', "select" => 'user', 'group' => 'user', "order" => '2', "count" => '*'];

    $sql_t = 'select DISTINCT user, count(*) as count from in_process group by user order by count desc';

    $tab = super_function($apiParams, [], $sql_t);

    $processNew = array_column($tab, 'count', 'user');

    return $processNew;
}

function get_lang_in_process_by_year($code, $year_y = "all"): array
{

    static $cache = [];

    if (!empty($cache[$code][$year_y] ?? [])) return $cache[$code][$year_y];

    $query = "select * from in_process where lang = ?";

    $apiParams = ['get' => 'in_process', 'lang' => $code];

    $params = [$code];

    if (isvalid($year_y)) {
        $query .= " AND YEAR(add_date) = ?";
        $params[] = $year_y;
        $apiParams['year'] = $year_y;
    }

    $data = super_function($apiParams, $params, $query);

    $cache[$code][$year_y] = $data;

    return $cache[$code][$year_y];
}

function get_lang_in_process($code): array
{

    static $cache = [];

    if (!empty($cache[$code] ?? [])) return $cache[$code];

    $query = "select * from in_process where lang = ?";

    $apiParams = ['get' => 'in_process', 'lang' => $code];

    $params = [$code];

    $data = super_function($apiParams, $params, $query);

    $cache[$code] = $data;

    return $data;
}
