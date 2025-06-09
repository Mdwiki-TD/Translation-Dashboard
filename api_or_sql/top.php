<?php

namespace SQLorAPI\TopData;

/*

Usage:

use function SQLorAPI\TopData\get_td_or_sql_top_lang_of_users;
use function SQLorAPI\TopData\get_td_or_sql_top_users;
use function SQLorAPI\TopData\get_td_or_sql_top_langs;
use function SQLorAPI\TopData\get_td_or_sql_status;

*/

use function SQLorAPI\Get\super_function;
use function SQLorAPI\Get\isvalid;

function get_td_or_sql_top_lang_of_users($users_original)
{
    // ---
    $users = (count($users_original) > 50) ? [] : $users_original;
    // ---
    $api_params = ['get' => 'top_lang_of_users', 'users' => $users];
    // ---
    $query_params = [];
    $query_line = "";
    // ---
    if (!empty($users) && is_array($users)) {
        $placeholders = rtrim(str_repeat('?,', count($users)), ',');
        $query_line = " AND p.user IN ($placeholders)";
        $query_params = $users;
    }
    // ---
    $query = <<<SQL
        SELECT user, lang, cnt
        FROM (
            SELECT p.user, p.lang, COUNT(p.target) AS cnt,
                ROW_NUMBER() OVER (PARTITION BY p.user ORDER BY COUNT(p.target) DESC) AS rn
            FROM pages p
            WHERE p.target != ''
            AND p.target IS NOT NULL
            $query_line
            GROUP BY p.user, p.lang
        ) AS ranked
        WHERE rn = 1
        ORDER BY cnt DESC;
    SQL;
    // ---
    $data = super_function($api_params, $query_params, $query);
    // ---
    // [{"user":"Subas Chandra Rout","lang":"or","cnt":1906},{"user":"Pranayraj1985","lang":"te","cnt":401} ...
    // var_export(json_encode($data));
    // ---
    if ($users != $users_original) {
        $data = array_filter($data, function ($item) use ($users_original) {
            return in_array($item['user'], $users_original);
        });
    }
    // ---
    return $data;
}

function add_top_params($query, $params, $to_add)
{
    $top_params = [
        "year" => "YEAR(p.pupdate)",
        "user_group" => "u.user_group",
        "cat" => "p.cat"
    ];
    // ---
    foreach ($top_params as $key => $column) {
        if (isvalid($to_add[$key] ?? '')) {
            $query .= " AND $column = ?";
            $params[] = $to_add[$key];
        }
    }
    // ---
    return ["qua" => $query, "params" => $params];
}

function top_query($select)
{
    // ---
    $select_field = ($select === 'user') ? 'p.user' : 'p.lang';
    // ---
    $query = <<<SQL
        SELECT
            $select_field,
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
    // ---
    return $query;
}

function get_td_or_sql_top_users($year, $user_group, $cat)
{
    // ---
    $to_add = ["year" => $year, "user_group" => $user_group, "cat" => $cat];
    // ---
    $api_params = ['get' => 'top_users', 'year' => $year, 'user_group' => $user_group, 'cat' => $cat];
    // ---
    $query = top_query('user');
    // ---
    $tab = add_top_params($query, [], $to_add);
    // ---
    $params = $tab['params'];
    $query = $tab['qua'];
    // ---
    $query .= " GROUP BY p.user ORDER BY 2 DESC";
    // ---
    $data = super_function($api_params, $params, $query);
    // ---
    $new_data = [];
    // ---
    foreach ($data as $item) {
        $item["count"] = intval($item["targets"]);
        $new_data[$item['user']] = $item;
    }
    // ---
    return $new_data;
}

function get_td_or_sql_top_langs($year, $user_group, $cat)
{
    // ---
    $to_add = ["year" => $year, "user_group" => $user_group, "cat" => $cat];
    // ---
    $api_params = ['get' => 'top_langs', 'year' => $year, 'user_group' => $user_group, 'cat' => $cat];
    // ---
    $query = top_query('lang');
    // ---
    $tab = add_top_params($query, [], $to_add);
    // ---
    $params = $tab['params'];
    $query = $tab['qua'];
    // ---
    $query .= " GROUP BY p.lang ORDER BY 2 DESC";
    // ---
    $data = super_function($api_params, $params, $query);
    // ---
    $new_data = [];
    // ---
    foreach ($data as $item) {
        $item["count"] = intval($item["targets"]);
        $new_data[$item['lang']] = $item;
    }
    // ---
    return $new_data;
}

function get_td_or_sql_status($year, $user_group, $cat)
{
    // ---
    $to_add = ["year" => $year, "user_group" => $user_group, "cat" => $cat];
    // ---
    $api_params = ['get' => 'status', 'year' => $year, 'user_group' => $user_group, 'cat' => $cat];
    // ---
    $query = <<<SQL
        SELECT LEFT(p.pupdate, 7) as date, COUNT(*) as count

        FROM pages p

        LEFT JOIN users u
            ON p.user = u.username

        WHERE p.target != ''
        ;
    SQL;
    // ---
    $tab = add_top_params($query, [], $to_add);
    // ---
    $params = $tab['params'];
    $query = $tab['qua'];
    // ---
    $query .= " GROUP BY LEFT(p.pupdate, 7) ORDER BY LEFT(p.pupdate, 7) ASC";
    // ---
    $data = super_function($api_params, $params, $query);
    // ---
    $new_data = [];
    // ---
    foreach ($data as $item) {
        $new_data[$item['date']] = intval($item["count"]);
    }
    // ---
    return $new_data;
}
