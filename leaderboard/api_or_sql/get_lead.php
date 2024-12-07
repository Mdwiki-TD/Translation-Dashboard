<?php

namespace Leaderboard\GetLead;

/*

Usage:

use function Leaderboard\GetLead\get_leaderboard_table;

*/

use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;

$from_api  = (isset($_GET['from_api'])) ? true : false;

function makeSqlQuery($year, $project)
{
    $query_project = "SELECT p.title,
        p.target, p.cat, p.lang, p.word, YEAR(p.pupdate) AS pup_y, p.user, LEFT(p.pupdate, 7) as m, u.user_group
        FROM pages p, users u
        WHERE p.target != ''
    ";

    $query = "SELECT p.title,
        p.target, p.cat, p.lang, p.word, YEAR(p.pupdate) AS pup_y, p.user, LEFT(p.pupdate, 7) as m,
        (SELECT u.user_group FROM users u WHERE p.user = u.username) AS user_group
        FROM pages p
        WHERE p.target != ''
    ";

    $params = [];

    if ($project != 'all' && !empty($project)) {
        $query = $query_project;
        $query .= "AND p.user = u.username \n";
        $query .= "AND u.user_group = ? \n";
        $params[] = $project;
    }

    if ($year != 'all' && !empty($year)) {
        $query .= "AND YEAR(p.pupdate) = ? \n";
        $params[] = $year;
    }

    return [
        'query' => $query,
        'params' => $params
    ];
}

function le_from_sql($year, $project)
{
    // ---
    $qua_data = makeSqlQuery($year, $project);
    // ---
    $qua_query = $qua_data['query'];
    $qua_params = $qua_data['params'];
    // ---
    $data = fetch_query($qua_query, $qua_params);
    // ---
    return $data;
}


function le_td_api($year, $project)
{
    // ---
    $api_params = ['get' => 'leaderboard_table'];
    // ----
    if ($year != 'all' && !empty($year)) {
        $api_params['year'] = $year;
    }
    // ---
    if ($project != 'all' && !empty($project)) {
        $api_params['user_group'] = $project;
    }
    // ---
    $data = get_td_api($api_params);
    // ---
    return $data;
}

function get_leaderboard_table($year, $project)
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = le_td_api($year, $project);
    } else {
        $data = le_from_sql($year, $project);
    }
    // ---
    return $data;
}