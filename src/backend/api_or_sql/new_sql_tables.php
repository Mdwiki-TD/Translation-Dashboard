<?php

namespace SQLorAPI\Funcs;

/*

Usage:

use function SQLorAPI\Funcs\exists_by_qids_query_and_category;
use function SQLorAPI\Funcs\exists_by_qids_query;
use function SQLorAPI\Funcs\get_missing_exists_statics;

*/

use function SQLorAPI\Get\super_function;
use function SQLorAPI\Get\isvalid;


function exists_by_qids_query_and_category($lang, $category)
{
    // ---
    // http://localhost:9001/api.php?get=exists_by_qids&lang=ar&category=RTT&target=not_empty
    static $data2 = [];
    // ---
    if (!empty($data2[$lang . $category] ?? [])) {
        return $data2[$lang . $category];
    }
    // ---
    $api_params = ['get' => 'exists_by_qids', 'lang' => $lang, 'category' => $category, 'target' => 'not_empty'];
    // ---
    $query = <<<SQL
        SELECT a.qid, a.title, a.category, t.code, t.target
            FROM all_qids_titles a
            JOIN all_qids_exists t
            ON t.qid = a.qid
        WHERE t.code = ?
        AND a.category = ?
        AND (t.target != '' AND t.target IS NOT NULL)
    SQL;
    // ---
    $params = [$lang, $category];
    // ---
    $u_data = super_function($api_params, $params, $query, "all_qids_titles");
    // ---
    $data2[$lang . $category] = $u_data;
    // ---
    return $u_data;
}

function exists_by_qids_query($lang)
{
    // ---
    // http://localhost:9001/api.php?get=exists_by_qids&lang=ar&target=not_empty
    // ---
    static $data2 = [];
    // ---
    if (!empty($data2[$lang] ?? [])) {
        return $data2[$lang];
    }
    // ---
    $api_params = ['get' => 'exists_by_qids', 'lang' => $lang, 'target' => 'not_empty'];
    // ---
    $query = <<<SQL
        SELECT a.qid, a.title, a.category, t.code, t.target
            FROM all_qids_titles a
            JOIN all_qids_exists t
            ON t.qid = a.qid
        WHERE t.code = ?

        AND (t.target != '' AND t.target IS NOT NULL)
    SQL;
    // ---
    $params = [$lang];
    // ---
    $u_data = super_function($api_params, $params, $query, "all_qids_titles");
    // ---
    $data2[$lang] = $u_data;
    // ---
    return $u_data;
}


function get_missing_exists_statics($category)
{
    // ---
    if ($category === null) {
        $category = "RTT";
    }
    // ---
    // http://localhost:9001/api.php?get=exists_by_qids&lang=ar&category=RTT&target=not_empty
    static $data2 = [];
    // ---
    if (!empty($data2[$category] ?? [])) {
        return $data2[$category];
    }
    // ---
    $api_params = ['get' => 'missing_exists_statics', 'category' => $category];
    // ---
    $query = <<<SQL
        SELECT
            a.code AS language_code,
            la.autonym AS autonym,
            la.name AS language_name,
            COUNT(a.article_id) AS available_title_count,
            (total.total_rtt - COUNT(a.article_id)) AS missing_title_count,
            total.total_rtt as total
        FROM
            all_exists a
        CROSS JOIN (
            SELECT COUNT(DISTINCT article_id) AS total_rtt
            FROM category_members
            WHERE category = ?
        ) total
        LEFT JOIN langs la ON la.code = a.code
        WHERE
            a.article_id IN (
                SELECT c.article_id
                FROM category_members c
                WHERE c.category = ?
            )
        GROUP BY
            a.code, la.autonym, la.name, total.total_rtt
        ORDER BY
            available_title_count DESC;
    SQL;
    // ---
    $params = [$category, $category];
    // ---
    $u_data = super_function($api_params, $params, $query, "category_members");
    // ---
    $data2[$category] = $u_data;
    // ---
    return $u_data;
}
