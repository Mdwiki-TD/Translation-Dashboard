<?php

namespace App\SQLorAPI\GetLead;

use function App\SQLorAPI\Get\superFunction;
use function App\SQLorAPI\Get\isValid;

function makeSqlQuery($year, $userGroup, $cat)
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

    if (isValid($userGroup)) {
        $query .= " AND u.user_group = ?";
        $params[] = $userGroup;
    }

    if (isValid($year)) {
        $query .= " AND YEAR(p.pupdate) = ? ";
        $params[] = $year;
    }

    if (isValid($cat)) {
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

function makeApiParams($year, $userGroup, $cat)
{

    $apiParams = ['get' => 'leaderboard_table'];
    // ----
    if (isValid($year)) {
        $apiParams['year'] = $year;
    }

    if (isValid($userGroup)) {
        $apiParams['user_group'] = $userGroup;
    }

    if (isValid($cat)) {
        $apiParams['cat'] = $cat;
    }

    return $apiParams;
}

# @deprecated
function getLeaderboardTable($year, $userGroup, $cat)
{

    $apiParams = makeApiParams($year, $userGroup, $cat);

    $quaData = makeSqlQuery($year, $userGroup, $cat);

    $quaQuery = $quaData['query'];
    $quaParams = $quaData['params'];

    $data = superFunction($apiParams, $quaParams, $quaQuery);

    return $data;
}
