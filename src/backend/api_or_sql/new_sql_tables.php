<?php

namespace SQLorAPI\Funcs;

/*

Usage:

use function SQLorAPI\Funcs\missing_by_lang_and_category;
use function SQLorAPI\Funcs\exists_by_lang_and_category;
use function SQLorAPI\Funcs\count_category_members;

*/

use function SQLorAPI\Get\super_function;

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

        JOIN qids q                     ON q.title      = c.article_id
        LEFT JOIN all_qids_exists aq    ON aq.qid       = q.qid AND aq.code = ?

        LEFT JOIN assessments ase       ON ase.title    = c.article_id
        LEFT JOIN enwiki_pageviews ep   ON ep.title     = c.article_id
        LEFT JOIN refs_counts rc        ON rc.r_title   = c.article_id
        LEFT JOIN words w               ON w.w_title    = c.article_id
        WHERE
            c.category = ?
        AND aq.target IS NULL

        /* to work with valid langs */
        AND EXISTS ( SELECT 1 FROM langs la WHERE la.code = ? )
    SQL;
    // ---
    $params = [$lang_code, $category, $lang_code];
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

        JOIN qids q                ON q.title = c.article_id
        LEFT JOIN all_qids_exists aq    ON aq.qid = q.qid AND aq.code = ?

        LEFT JOIN assessments ase       ON ase.title    = c.article_id
        LEFT JOIN enwiki_pageviews ep   ON ep.title     = c.article_id
        LEFT JOIN refs_counts rc        ON rc.r_title   = c.article_id
        LEFT JOIN words w               ON w.w_title    = c.article_id
        WHERE
            c.category = ?
        AND aq.target IS NOT NULL

        /* to work with valid langs */
        AND EXISTS ( SELECT 1 FROM langs la WHERE la.code = ? )
    SQL;
    // ---
    $params = [$lang_code, $category, $lang_code];
    // ---
    $u_data = super_function($api_params, $params, $query, "category_members");
    // ---
    return $u_data;
}

function count_category_members($category)
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
    // ---`
    $query = <<<SQL
        SELECT
            COUNT(c.article_id) AS members
        FROM
            category_members c
        where c.category = ?
    SQL;
    // ---
    $params = [$category];
    // ---
    $u_data = super_function([], $params, $query, "category_members");
    // ---
    $data2[$category] = $u_data;
    // ---
    return $u_data;
}


function statics_by_category($category)
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
    $api_params = ['get' => 'statics_by_category', 'category' => $category];
    // ---
    $query = <<<SQL
        SELECT
            aq.code AS language_code,
            COUNT(*) AS available_title_count
        FROM
            category_members c
            JOIN qids q ON q.title = c.article_id
            JOIN all_qids_exists aq ON aq.qid = q.qid
        WHERE
            c.category = ?
        GROUP BY
            aq.code
        ORDER BY
            available_title_count ASC;
    SQL;
    // ---
    $params = [$category];
    // ---
    $u_data = super_function($api_params, $params, $query, "category_members");
    // ---
    $data2[$category] = $u_data;
    // ---
    return $u_data;
}
