<?php

namespace SQLorAPI\Funcs;

/*

Usage:

use function SQLorAPI\Funcs\exists_by_qids_query_and_category;
use function SQLorAPI\Funcs\exists_by_qids_query;

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
    $u_data = super_function($api_params, $params, $query, $no_refind = false, $table_name = "all_qids_titles");
    // ---
    $data2[$lang . $category] = $u_data;
    // ---
    return $u_data;
}

function exists_by_qids_query($lang, $category)
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
    $u_data = super_function($api_params, $params, $query, $no_refind = false, $table_name = "all_qids_titles");
    // ---
    $data2[$lang] = $u_data;
    // ---
    return $u_data;
}
