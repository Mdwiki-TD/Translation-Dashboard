<?php

namespace App\SQLorAPI\GetDataTab;

use function App\SQLorAPI\Get\superFunction;
use function App\SQLorAPI\Get\isValid;


function getViews($year, $lang)
{

    static $cache = [];

    if (!empty($cache[$year . $lang] ?? [])) {
        return $cache[$year . $lang];
    }

    $apiParams = ['get' => 'views_new'];

    $query2 = <<<SQL
        SELECT p.title, v.target, v.lang, v.views
        FROM views_new_all v
        LEFT JOIN pages p
            ON p.target = v.target
            AND p.lang = v.lang
    SQL;

    $queryComplate = [];

    $sqlParams = [];

    if (isValid($lang)) {
        $apiParams['lang'] = $lang;
        $sqlParams[] = $lang;

        $queryComplate[] = " v.lang = ? ";
    }

    if (isValid($year)) {
        $apiParams['year'] = $year;
        $sqlParams[] = $year;

        $queryComplate[] = " YEAR(p.pupdate) = ? ";
    }

    if (!empty($queryComplate)) {
        $query2 .= " WHERE " . implode(" AND ", $queryComplate);
    }

    $data = superFunction($apiParams, $sqlParams, $query2);

    $cache[$year . $lang] = $data;

    return $data;
}

function getSettings()
{

    static $sqlSettings = [];

    if (!empty($sqlSettings)) {
        return $sqlSettings;
    }

    $query = "select id, title, displayed, value, Type from settings";

    $apiParams = ['get' => 'settings'];

    $sqlSettings = superFunction($apiParams, [], $query);

    return $sqlSettings;
}

function getUsersNoInprocess()
{

    static $users = [];

    if (!empty($users)) return $users;

    $apiParams = ['get' => 'users_no_inprocess'];
    $query = "SELECT id, user, is_active FROM users_no_inprocess order by id";
    $users = superFunction($apiParams, [], $query);

    return $users;
}

function getFullTranslators($column = null)
{

    static $fullTr = [];

    if (!empty($fullTr)) return $fullTr;

    $apiParams = ['get' => 'full_translators'];
    $query = "SELECT id, user, is_active FROM full_translators";
    $fullTr = superFunction($apiParams, [], $query);

    if ($column) {
        return array_column($fullTr, $column);
    }

    return $fullTr;
}

function getCountPages()
{

    static $countPages = [];

    if (!empty($countPages ?? [])) {
        return $countPages;
    }

    $apiParams = ['get' => 'count_pages'];
    $query = <<<SQL
        SELECT DISTINCT user, count(target) as count from pages group by user order by count desc
    SQL;

    $data = superFunction($apiParams, [], $query);

    $data = array_column($data, 'count', 'user');

    arsort($data);

    // print_r($data);

    $countPages = $data;

    return $data;
}
