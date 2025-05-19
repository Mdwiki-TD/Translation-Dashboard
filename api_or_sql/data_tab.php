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
use function SQLorAPI\GetDataTab\get_td_or_sql_users_by_wiki;
use function SQLorAPI\GetDataTab\get_td_or_sql_count_pages;

*/

use function SQLorAPI\Get\super_function;
use function SQLorAPI\Get\isvalid;

$data_tab = [];

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
    global $from_api, $data_tab;
    // ---
    if (!empty($data_tab['sql_views' . $year . $lang] ?? [])) {
        return $data_tab['sql_views' . $year . $lang];
    }
    // ---
    $api_params = ['get' => 'views_new'];
    // ---
    if (isvalid($year)) {
        $api_params['year'] = $year;
    }
    // ---
    if (isvalid($lang)) {
        $api_params['lang'] = $lang;
    }
    // ---
    $query2 = <<<SQL
        SELECT p.title, v.target, v.lang, v.views
        FROM views_new_all v
        LEFT JOIN pages p
            ON p.target = v.target
            AND p.lang = v.lang
    SQL;
    //---
    $params = [];
    //---
    if (isvalid($lang)) {
        $query2 .= " WHERE v.lang = ? \n";
        $params[] = $lang;
    }
    //---
    if (isvalid($year)) {
        $query2 .= " AND YEAR(p.pupdate) = ? \n";
        $params[] = $year;
    }
    //---
    $data = super_function($api_params, $params, $query2);
    // ---
    $data_tab['sql_views' . $year . $lang] = $data;
    // ---
    return $data;
}

function get_td_or_sql_settings()
{
    // ---
    $settingsx = [];
    // ---
    // if (!empty($settingsx)) { return $settingsx; }
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
    static $projects = [];
    // ---
    if (!empty($projects ?? [])) {
        return $projects;
    }
    // ---
    $api_params = ['get' => 'projects'];
    $query = "select g_id, g_title from projects";
    //---
    $projects = super_function($api_params, [], $query);
    // ---
    return $projects;
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

function make_users_by_wiki_query($year, $user_group, $cat)
{
    // ---
    $query_params = [];
    $query_complate = [];
    // ---
    if (isvalid($year)) {
        $query_complate[] = " YEAR(pupdate) = ?";
        $query_params[] = $year;
    }
    // ---
    if (isvalid($user_group)) {
        $query_complate[] = " u.user_group = ?";
        $query_params[] = $user_group;
    }
    // ---
    if (isvalid($cat)) {
        $query_complate[] = " cat = ?";
        $query_params[] = $cat;
    }
    // ---
    $query_complate_text = "";
    // ---
    if (!empty($query_complate)) {
        $query_complate_text = " WHERE " . implode(" AND ", $query_complate);
    }
    // ---
    $query_o = <<<SQL
        SELECT user, lang, YEAR(pupdate) AS year, COUNT(target) AS target_count
        FROM pages
        LEFT JOIN users u
            ON user = u.username
        $query_complate_text
        GROUP BY user, lang
        ORDER BY 1 DESC
    SQL;
    //---
    $query = <<<SQL
        SELECT user, lang, year, MAX(target_count) AS max_target, sum(target_count) AS sum_target
            FROM (
                $query_o
            ) AS subquery
        GROUP BY user
        ORDER BY 4 DESC
    SQL;
    //---
    return ['query' => $query, 'query_params' => $query_params];
}

function get_td_or_sql_users_by_wiki($year, $user_group, $cat)
{
    // ---
    static $users_by_wiki = [];
    // ---
    if (!empty($users_by_wiki ?? [])) {
        return $users_by_wiki;
    }
    // ---
    $api_params = ['get' => 'users_by_wiki', 'year' => $year, 'user_group' => $user_group, 'cat' => $cat];
    // ---
    $tata = make_users_by_wiki_query($year, $user_group, $cat);
    // ---
    $query = $tata['query'];
    $query_params = $tata['query_params'];
    // ---
    $data = super_function($api_params, $query_params, $query);
    // ---
    $users_by_wiki = $data;
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
