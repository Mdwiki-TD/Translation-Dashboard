<?php

namespace SQLorAPI\GetLead;

/*

Usage:

use function SQLorAPI\GetLead\get_leaderboard_table;

*/

use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;


function isvalid($str)
{
    return !empty($str) && strtolower($str) != 'all';
}

function makeSqlQuery($year, $user_group, $cat)
{
    $params = [];

    $query = "SELECT p.title,
        p.target, p.cat, p.lang, p.word, YEAR(p.pupdate) AS pup_y, p.user, LEFT(p.pupdate, 7) as m,
        u.user_group
        FROM pages p
        LEFT JOIN users u ON p.user = u.username
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

    $query .= " ORDER BY 1 DESC";

    return [
        'query' => $query,
        'params' => $params
    ];
}

function makeSqlQuery_old($year, $user_group, $cat)
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

    if (isvalid($user_group)) {
        $query = $query_project;
        $query .= "AND p.user = u.username \n";
        $query .= "AND u.user_group = ? \n";
        $params[] = $user_group;
    }

    if (isvalid($year)) {
        $query .= "AND YEAR(p.pupdate) = ? \n";
        $params[] = $year;
    }


    if (isvalid($cat)) {
        $query .= "AND p.cat = ? \n";
        $params[] = $year;
    }

    return [
        'query' => $query,
        'params' => $params
    ];
}

function le_from_sql($year, $user_group, $cat)
{
    // ---
    $qua_data = makeSqlQuery($year, $user_group, $cat);
    // ---
    $qua_query = $qua_data['query'];
    $qua_params = $qua_data['params'];
    // ---
    $data = fetch_query($qua_query, $qua_params);
    // ---
    return $data;
}


function le_td_api($year, $user_group, $cat)
{
    // ---
    $api_params = ['get' => 'leaderboard_table'];
    // ----
    if (isvalid($year)) {
        $api_params['year'] = $year;
    }
    // ---
    if (isvalid($user_group)) {
        $api_params['user_group'] = $user_group;
    }
    // ---
    if (isvalid($cat)) {
        $api_params['cat'] = $cat;
    }
    $data = get_td_api($api_params);
    // ---
    return $data;
}

function get_leaderboard_table($year, $user_group, $cat)
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = le_td_api($year, $user_group, $cat);
    } else {
        $data = le_from_sql($year, $user_group, $cat);
    }
    // ---
    return $data;
}
