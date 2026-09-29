<?php

namespace App\SQLorAPI\TopData;

use function App\SQLorAPI\Get\superFunction;
use function App\SQLorAPI\Get\isValid;

function getTopLangOfUsers($usersOriginal)
{

    $users = (count($usersOriginal) > 50) ? [] : $usersOriginal;

    $apiParams = ['get' => 'top_lang_of_users', 'users' => $users];

    $queryParams = [];
    $queryLine = "";

    if (!empty($users) && is_array($users)) {
        $placeholders = rtrim(str_repeat('?,', count($users)), ',');
        $queryLine = " AND p.user IN ($placeholders)";
        $queryParams = $users;
    }

    $query = <<<SQL
        SELECT user, lang, cnt
        FROM (
            SELECT p.user, p.lang, COUNT(p.target) AS cnt,
                ROW_NUMBER() OVER (PARTITION BY p.user ORDER BY COUNT(p.target) DESC) AS rn
            FROM pages p
            WHERE p.target != ''
            AND p.target IS NOT NULL
            $queryLine
            GROUP BY p.user, p.lang
        ) AS ranked
        WHERE rn = 1
        ORDER BY cnt DESC;
    SQL;

    $data = superFunction($apiParams, $queryParams, $query);

    // [{"user":"Subas Chandra Rout","lang":"or","cnt":1906},{"user":"Pranayraj1985","lang":"te","cnt":401} ...
    // var_export(json_encode($data));

    if ($users != $usersOriginal) {
        $data = array_filter($data, function ($item) use ($usersOriginal) {
            return in_array($item['user'], $usersOriginal);
        });
    }

    return $data;
}

function addTopParams($query, $params, $toAdd)
{
    $topParams = [
        "year" => "YEAR(p.pupdate)",
        "month" => "MONTH(p.pupdate)",
        "user_group" => "u.user_group",
        "cat" => "p.cat"
    ];

    foreach ($topParams as $key => $column) {
        if (isValid($toAdd[$key] ?? '')) {
            $query .= " AND $column = ?";
            $params[] = $toAdd[$key];
        }
    }

    return [$query, $params];
}

function topQuery($select)
{

    $selectField = ($select === 'user') ? 'p.user' : 'p.lang';

    $query = <<<SQL
        SELECT
            $selectField,
            COUNT(p.target) AS targets,
            SUM(CASE
                WHEN p.word IS NOT NULL AND p.word != 0 AND p.word != '' THEN p.word
                WHEN translate_type = 'all' THEN w.w_all_words
                ELSE w.w_lead_words
            END) AS words,
            SUM(
                CASE
                    WHEN v.views IS NULL OR v.views = '' THEN 0
                    ELSE CAST(v.views AS UNSIGNED)
                END
                ) AS views

        FROM pages p

        LEFT JOIN users u
            ON p.user = u.username

        LEFT JOIN words w
            ON w.w_title = p.title

        LEFT JOIN views_new_all v
            ON p.target = v.target AND p.lang = v.lang

        WHERE p.target != '' AND p.target IS NOT NULL
        AND p.user != '' AND p.user IS NOT NULL
        AND p.lang != '' AND p.lang IS NOT NULL
        SQL;

    return $query;
}

function getTopUsers($year, $userGroup, $cat, $month = null)
{

    $toAdd = [
        "year" => $year,
        "user_group" => $userGroup,
        "cat" => $cat,
        "month" => $month,
    ];

    $apiParams = [
        'get' => 'top_users',
        'year' => $year,
        'user_group' => $userGroup,
        'cat' => $cat,
        'month' => $month,
    ];

    $query = topQuery('user');

    [$query, $params] = addTopParams($query, [], $toAdd);

    $query .= " GROUP BY p.user ORDER BY 2 DESC";

    $data = superFunction($apiParams, $params, $query);

    $newData = [];

    foreach ($data as $item) {
        $item["count"] = intval($item["targets"]);
        $newData[$item['user']] = $item;
    }

    return $newData;
}

function getTopLangs($year, $userGroup, $cat, $month = null): array
{

    $toAdd = [
        "year" => $year,
        "user_group" => $userGroup,
        "cat" => $cat,
        'month' => $month,
    ];

    $apiParams = [
        'get' => 'top_langs',
        'year' => $year,
        'user_group' => $userGroup,
        'cat' => $cat,
        'month' => $month,
    ];

    $query = topQuery('lang');

    [$query, $params] = addTopParams($query, [], $toAdd);

    $query .= " GROUP BY p.lang ORDER BY 2 DESC";

    $data = superFunction($apiParams, $params, $query);

    $newData = [];

    foreach ($data as $item) {
        $item["count"] = intval($item["targets"]);
        $newData[$item['lang']] = $item;
    }

    return $newData;
}

function getStatus($year, $userGroup, $cat): array
{

    $toAdd = ["year" => $year, "user_group" => $userGroup, "cat" => $cat];

    $apiParams = ['get' => 'status', 'year' => $year, 'user_group' => $userGroup, 'cat' => $cat];

    $query = <<<SQL
        SELECT LEFT(p.pupdate, 7) as date, COUNT(*) as count

        FROM pages p

        LEFT JOIN users u
            ON p.user = u.username

        WHERE p.target != ''

    SQL;

    [$query, $params] = addTopParams($query, [], $toAdd);

    $query .= " GROUP BY 1 ORDER BY 1 ASC";

    $data = superFunction($apiParams, $params, $query);

    // var_export(json_encode($params));
    // echo $query . "<br>";
    // var_export(json_encode($data));

    $newData = [];

    foreach ($data as $item) {
        $newData[$item['date']] = intval($item["count"]);
    }

    return $newData;
}
