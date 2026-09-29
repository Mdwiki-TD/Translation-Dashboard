<?php

namespace App\SQLorAPI\GetDataTab;

use function App\SQLorAPI\Get\superFunction;
use function App\SQLorAPI\Get\isValid;


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
