<?php

namespace SQLorAPI\GetDataTab;

/*

Usage:

use function SQLorAPI\GetDataTab\get_camps_to_cat;
use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;
use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function SQLorAPI\GetDataTab\get_td_or_sql_users_no_inprocess;
use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function SQLorAPI\GetDataTab\get_td_or_sql_categories;
use function SQLorAPI\GetDataTab\get_td_or_sql_projects;
use function SQLorAPI\GetDataTab\get_td_or_sql_settings;
use function SQLorAPI\GetDataTab\get_td_or_sql_views;
use function SQLorAPI\GetDataTab\get_td_or_sql_titles_infos;
use function SQLorAPI\GetDataTab\get_td_or_sql_count_pages;
use function SQLorAPI\GetDataTab\get_td_or_sql_langs;

*/

use function SQLorAPI\Get\super_function;
use function SQLorAPI\Get\isvalid;

function get_td_or_sql_titles_infos()
{
    // ---
    static $titlesinfos = [];
    // ---
    if (!empty($titlesinfos ?? [])) {
        return $titlesinfos;
    }
    // ---
    $api_params = ['get' => 'titles'];
    // ---
    $qua = <<<SQL
        select
            ase.title AS title,
            ase.importance AS importance,
            rc.r_lead_refs AS r_lead_refs,
            rc.r_all_refs AS r_all_refs,
            ep.en_views AS en_views,
            w.w_lead_words AS w_lead_words,
            w.w_all_words AS w_all_words,
            q.qid AS qid
        from
            assessments ase
            left join enwiki_pageviews ep on ase.title = ep.title
            left join qids q on q.title = ase.title
            left join refs_counts rc on rc.r_title = ase.title
            left join words w on w.w_title = ase.title
    SQL;
    // ---
    $data = super_function($api_params, [], $qua);
    // ---
    $titlesinfos = $data;
    // ---
    return $titlesinfos;
}

function get_td_or_sql_views($year, $lang)
{
    // ---
    static $cache = [];
    // ---
    if (!empty($cache[$year . $lang] ?? [])) {
        return $cache[$year . $lang];
    }
    // ---
    $api_params = ['get' => 'views_new'];
    // ---
    $query2 = <<<SQL
        SELECT p.title, v.target, v.lang, v.views
        FROM views_new_all v
        LEFT JOIN pages p
            ON p.target = v.target
            AND p.lang = v.lang
    SQL;
    //---
    $query_complate = [];
    //---
    $sql_params = [];
    //---
    if (isvalid($lang)) {
        $api_params['lang'] = $lang;
        $sql_params[] = $lang;
        // ---
        $query_complate[] = " v.lang = ? ";
    }
    //---
    if (isvalid($year)) {
        $api_params['year'] = $year;
        $sql_params[] = $year;
        // ---
        $query_complate[] = " YEAR(p.pupdate) = ? ";
    }
    //---
    if (!empty($query_complate)) {
        $query2 .= " WHERE " . implode(" AND ", $query_complate);
    }
    // ---
    $data = super_function($api_params, $sql_params, $query2);
    // ---
    $cache[$year . $lang] = $data;
    // ---
    return $data;
}

function get_td_or_sql_settings()
{
    // ---
    static $sql_settings = [];
    // ---
    if (!empty($sql_settings)) {
        return $sql_settings;
    }
    // ---
    $query = "select id, title, displayed, value, Type from settings";
    // ---
    $api_params = ['get' => 'settings'];
    // ---
    $sql_settings = super_function($api_params, [], $query);
    // ---
    return $sql_settings;
}

function get_td_or_sql_projects()
{
    // ---
    static $user_groups = [];
    // ---
    if (!empty($user_groups ?? [])) {
        return $user_groups;
    }
    // ---
    $api_params = ['get' => 'projects'];
    $query = "select g_id, g_title from projects";
    //---
    $user_groups = super_function($api_params, [], $query);
    // ---
    return $user_groups;
}

function get_td_or_sql_categories()
{
    // ---
    static $categories = [];
    // ---
    if (!empty($categories ?? [])) {
        return $categories;
    }
    // ---
    $api_params = ['get' => 'categories'];
    $query = "select id, category, category2, campaign, depth, is_default from categories";
    // ---
    $categories = super_function($api_params, [], $query);
    // ---
    return $categories;
}

function get_td_or_sql_qids()
{
    // ---
    static $sql_td_qids = [];
    // ---
    if (!empty($sql_td_qids)) return $sql_td_qids;
    // ---
    $api_params = ['get' => 'qids'];
    $query = "SELECT title, qid FROM qids";
    $data = super_function($api_params, [], $query);
    // ---
    $sql_td_qids = array_column($data, 'qid', 'title');
    // ---
    return $sql_td_qids;
}

function get_td_or_sql_users_no_inprocess()
{
    // ---
    static $users = [];
    // ---
    if (!empty($users)) return $users;
    // ---
    $api_params = ['get' => 'users_no_inprocess'];
    $query = "SELECT id, user, is_active FROM users_no_inprocess order by id";
    $users = super_function($api_params, [], $query);
    // ---
    return $users;
}

function get_td_or_sql_full_translators($column = null)
{
    // ---
    static $full_tr = [];
    // ---
    if (!empty($full_tr)) return $full_tr;
    // ---
    $api_params = ['get' => 'full_translators'];
    $query = "SELECT id, user, is_active FROM full_translators";
    $full_tr = super_function($api_params, [], $query);
    // ---
    if ($column) {
        return array_column($full_tr, $column);
    }
    // ---
    return $full_tr;
}

function get_td_or_sql_translate_type()
{
    // ---
    static $translate_type = [];
    // ---
    if (!empty($translate_type ?? [])) {
        return $translate_type;
    }
    // ---
    $api_params = ['get' => 'translate_type'];
    $query = "SELECT tt_title, tt_lead, tt_full FROM translate_type";
    //---
    $data = super_function($api_params, [], $query);
    // ---
    $translate_type = $data;
    // ---
    return $data;
}

function get_td_or_sql_count_pages()
{
    // ---
    static $count_pages = [];
    // ---
    if (!empty($count_pages ?? [])) {
        return $count_pages;
    }
    // ---
    $api_params = ['get' => 'count_pages'];
    $query = <<<SQL
        SELECT DISTINCT user, count(target) as count from pages group by user order by count desc
    SQL;
    //---
    $data = super_function($api_params, [], $query);
    // ---
    $data = array_column($data, 'count', 'user');
    // ---
    arsort($data);
    // ---
    // print_r($data);
    // ---
    $count_pages = $data;
    // ---
    return $data;
}

function get_td_or_sql_langs()
{
    // ---
    static $langs = [];
    // ---
    if (!empty($langs ?? [])) return $langs;
    // ---
    $api_params = ['get' => 'langs'];
    $query = "SELECT code, autonym, name, redirects FROM langs";
    // ---
    $data = super_function($api_params, [], $query);
    // ---
    $langs = array_column($data, null, 'code');
    // ---
    return $langs;
}

function get_qids($list)
{
    //---
    $sq_qids = get_td_or_sql_qids();
    //---
    $with_qids = [];
    $no_qids = [];
    // ---
    foreach ($list as $member) {
        $qid = $sq_qids[$member] ?? 0;
        if ($qid) {
            $with_qids[$member] = $qid;
        } else {
            $no_qids[] = $member;
        }
    }
    // ---
    return [
        "with_qids" => $with_qids,
        "no_qids" => $no_qids,
    ];
}
function get_camps_to_cat()
{
    static $s_camp_to_cat = [];
    if (!empty($s_camp_to_cat)) return $s_camp_to_cat;

    $categories_tab = get_td_or_sql_categories();
    $s_camp_to_cat = array_column($categories_tab, "category", 'campaign');

    return $s_camp_to_cat;
}

function get_endpoint()
{
    // ---
    static $settings1 = [];
    // ---
    if (empty($settings1)) {
        $settings1 = get_td_or_sql_settings();
        $settings1 = array_column($settings1, 'value', 'title');
    }
    // ---
    $use_mdwikicx = $settings1['use_mdwikicx'] ?? '0';
    // ---
    $endpoint = "https://medwiki.toolforge.org/w/index.php";
    // ---
    if ($use_mdwikicx != '0') {
        $endpoint = "https://mdwikicx.toolforge.org/w/index.php";
    };
    // ---
    return $endpoint;
}
