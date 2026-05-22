<?php

namespace SQLorAPI\Funcs;

/*

Usage:

use function SQLorAPI\Funcs\exists_by_qids_query;
use function SQLorAPI\Funcs\get_missing_exists_statics;
use function SQLorAPI\Funcs\missing_by_lang_and_category;
use function SQLorAPI\Funcs\exists_by_lang_and_category;

*/

use function SQLorAPI\Get\super_function;
// use function SQLorAPI\Get\isvalid;

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
    // ---
    // exists_by_qids
    // ---
    /*
        [
            { "name": "lang", "column": "t.code", "type": "text", "placeholder": "Language code", "no_mt_options": true },
            { "name": "category", "column": "aa.category", "type": "text", "placeholder": "Category", "no_mt_options": true },
            { "name": "campaign", "column": "campaign", "type": "text", "placeholder": "Campaign" },
            { "name": "order", "column": "order", "type": "text", "placeholder": "Order by", "no_select": true }
        ]
      */
    // ---
    $api_params = ['get' => 'exists_by_qids', 'lang' => $lang];
    // ---
    $query = <<<SQL
        SELECT
            qq.qid AS qid,
            q.title AS title,
            aa.category AS category,
            t.code AS code,
            t.target AS target
        FROM all_qids qq
            LEFT JOIN qids q            ON qq.qid = q.qid
            LEFT JOIN all_articles aa   ON aa.article_id = q.title
            JOIN all_qids_exists t      ON t.qid = qq.qid
        WHERE t.code = ?

        AND (t.target != '' AND t.target IS NOT NULL)
    SQL;
    // ---
    $params = [$lang];
    // ---
    $u_data = super_function($api_params, $params, $query, "all_qids_exists");
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
        JOIN langs la ON la.code = a.code
        WHERE
            a.article_id IN (
                SELECT c.article_id
                FROM category_members c
                WHERE c.category = ?
            )
        AND la.autonym IS NOT NULL
        GROUP BY
            a.code, la.autonym, la.name, total.total_rtt
        ORDER BY 4 DESC;
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


function missing_by_lang_and_category($lang_code, $category)
{
    // ---
    $api_params = ['get' => 'missing_by_lang_and_category', 'category' => $category, 'lang' => $lang_code];
    // ---
    $query = <<<SQL
        SELECT
            c.article_id AS title,
            c.category AS category,
            ase.importance,
            rc.r_lead_refs,
            rc.r_all_refs,
            ep.en_views,
            q.qid,
            w.w_lead_words,
            w.w_all_words
        FROM
            category_members c

        LEFT JOIN assessments ase       ON ase.title = c.article_id
        LEFT JOIN enwiki_pageviews ep   ON ep.title = c.article_id
        LEFT JOIN qids q                ON q.title = c.article_id
        LEFT JOIN refs_counts rc        ON rc.r_title = c.article_id
        LEFT JOIN words w               ON w.w_title = c.article_id

        WHERE
            c.category = ?
        AND NOT EXISTS (
            SELECT
                1
            FROM
                all_exists t
            WHERE
                t.article_id = c.article_id
                AND t.code = ?
        )
        AND NOT EXISTS (
            SELECT
                1
            FROM
                all_qids_exists aqe
            WHERE
                aqe.code = ?
                AND aqe.qid = q.qid
        )
        /* to work with valid langs */
        AND EXISTS ( SELECT 1 FROM langs la WHERE la.code = ? )
    SQL;
    // ---
    $params = [$category, $lang_code, $lang_code, $lang_code];
    // ---
    $u_data = super_function($api_params, $params, $query, "category_members");
    // ---
    return $u_data;
}

function exists_by_lang_and_category($lang_code, $category)
{
    // ---
    $api_params = ['get' => 'exists_by_lang_and_category', 'category' => $category, 'lang' => $lang_code];
    // ---
    $query = <<<SQL
        SELECT
            c.article_id AS title,
            c.category AS category,
            ase.importance,
            rc.r_lead_refs,
            rc.r_all_refs,
            ep.en_views,
            q.qid,
            w.w_lead_words,
            w.w_all_words,
            aq.target
        FROM
            category_members c
        JOIN
            all_exists t ON t.article_id = c.article_id

        LEFT JOIN assessments ase       ON ase.title = c.article_id
        LEFT JOIN enwiki_pageviews ep   ON ep.title = c.article_id
        LEFT JOIN qids q                ON q.title = c.article_id
        LEFT JOIN refs_counts rc        ON rc.r_title = c.article_id
        LEFT JOIN words w               ON w.w_title = c.article_id
        JOIN all_qids_exists aq    ON aq.qid = q.qid
        WHERE
            c.category = ?
        AND t.code = ?
        AND t.code = aq.code
    SQL;
    // ---
    $params = [$category, $lang_code];
    // ---
    $u_data = super_function($api_params, $params, $query, "category_members");
    // ---
    return $u_data;
}
