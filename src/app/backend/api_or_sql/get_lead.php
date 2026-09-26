<?php

namespace App\SQLorAPI\GetLead;

use function App\SQLorAPI\Get\super_function;
use function App\SQLorAPI\Get\isvalid;

function makeSqlQuery($year, $user_group, $cat)
{
    $params = [];

    $query = "SELECT p.title,
        p.target, p.cat, p.lang, p.word, YEAR(p.pupdate) AS pup_y, p.user, u.user_group, LEFT(p.pupdate, 7) as m, v.views
        FROM pages p
        LEFT JOIN users u
            ON p.user = u.username
        LEFT JOIN views_new_all v
            ON p.target = v.target
            AND p.lang = v.lang
        WHERE p.target != ''
    ";

    if (isvalid($user_group)) {
        $query .= " AND u.user_group = ?";
        $params[] = $user_group;
    }

    if (isvalid($year)) {
        $query .= " AND YEAR(p.pupdate) = ? ";
        $params[] = $year;
    }

    if (isvalid($cat)) {
        $query .= " AND p.cat = ? ";
        $params[] = $cat;
    }

    $query .= " \n group by v.target, v.lang \n";
    $query .= " ORDER BY 1 DESC";

    return [
        'query' => $query,
        'params' => $params
    ];
}

function makeApiParams($year, $user_group, $cat)
{

    $apiParams = ['get' => 'leaderboard_table'];
    // ----
    if (isvalid($year)) {
        $apiParams['year'] = $year;
    }

    if (isvalid($user_group)) {
        $apiParams['user_group'] = $user_group;
    }

    if (isvalid($cat)) {
        $apiParams['cat'] = $cat;
    }

    return $apiParams;
}

# @deprecated
function get_leaderboard_table($year, $user_group, $cat)
{

    $apiParams = makeApiParams($year, $user_group, $cat);

    $qua_data = makeSqlQuery($year, $user_group, $cat);

    $qua_query = $qua_data['query'];
    $qua_params = $qua_data['params'];

    $data = super_function($apiParams, $qua_params, $qua_query);

    return $data;
}
