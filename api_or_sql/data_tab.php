<?php

namespace SQLorAPI\GetDataTab;

/*

Usage:

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
    $qua_old = <<<SQL
        SELECT
            ase.title,
            ase.importance,
            rc.r_lead_refs,
            rc.r_all_refs,
            ep.en_views,
            w.w_lead_words,
            w.w_all_words,
            q.qid
        FROM assessments ase
        LEFT JOIN enwiki_pageviews ep ON ase.title = ep.title
        LEFT JOIN qids q ON q.title = ase.title
        LEFT JOIN refs_counts rc ON rc.r_title = ase.title
        LEFT JOIN words w ON w.w_title = ase.title
    SQL;
    // ---
    $qua = <<<SQL
        SELECT *
        FROM titles_infos
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
    static $settingsx = [];
    // ---
    if (!empty($settingsx)) {
        return $settingsx;
    }
    // ---
    $query = "select id, title, displayed, value, Type from settings";
    // ---
    $api_params = ['get' => 'settings'];
    // ---
    $settingsx = super_function($api_params, [], $query);
    // ---
    return $settingsx;
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
    $query = "select id, category, category2, campaign, depth, def from categories";
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
    $query = "SELECT * FROM users_no_inprocess order by id";
    $users = super_function($api_params, [], $query);
    // ---
    return $users;
}

function get_td_or_sql_full_translators()
{
    // ---
    static $full_tr = [];
    // ---
    if (!empty($full_tr)) return $full_tr;
    // ---
    $api_params = ['get' => 'full_translators'];
    $query = "SELECT * FROM full_translators";
    $full_tr = super_function($api_params, [], $query);
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
