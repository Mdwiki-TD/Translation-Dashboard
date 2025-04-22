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

use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;

$settings_tabe = array_column(get_td_api(['get' => 'settings']), 'value', 'title');
//---
$from_api  = (($settings_tabe['use_td_api'] ?? "") == "1") ? true : false;

$data_tab = [];

function isvalid($str)
{
    return !empty($str) && $str != 'All' && $str != 'all';
}

function get_td_or_sql_titles_infos()
{
    // ---
    global $from_api;
    // ---
    static $titlesinfos = [];
    // ---
    if (!empty($titlesinfos ?? [])) {
        return $titlesinfos;
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'titles']);
    } else {
        $qua = <<<SQL
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
        $data = fetch_query($qua);
    }
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
    if ($from_api) {
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
        $data = get_td_api($api_params);
    } else {
        //---
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
        $data = fetch_query($query2, $params);
    }
    // ---
    $data_tab['sql_views' . $year . $lang] = $data;
    // ---
    return $data;
}

function get_td_or_sql_settings()
{
    // ---
    global $from_api;
    // ---
    $settingsx = [];
    // ---
    // if (!empty($settingsx)) { return $settingsx; }
    // ---
    if ($from_api) {
        $settingsx = get_td_api(['get' => 'settings']);
    } else {
        $query = "select id, title, displayed, value, Type from settings";
        //---
        $settingsx = fetch_query($query);
    }
    // ---
    return $settingsx;
}
function get_td_or_sql_projects()
{
    // ---
    global $from_api;
    // ---
    static $projects = [];
    // ---
    if (!empty($projects ?? [])) {
        return $projects;
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'projects']);
    } else {
        $query = "select g_id, g_title from projects";
        //---
        $data = fetch_query($query);
    }
    // ---
    $projects = $data;
    // ---
    return $data;
}
function get_td_or_sql_categories()
{
    // ---
    global $from_api;
    // ---
    static $categories = [];
    // ---
    if (!empty($categories ?? [])) {
        return $categories;
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'categories']);
    } else {
        $query = "select id, category, category2, campaign, depth, def from categories";
        //---
        $data = fetch_query($query);
    }
    // ---
    $categories = $data;
    // ---
    return $data;
}
function get_td_or_sql_qids()
{
    // ---
    global $from_api;
    // ---
    static $sql_td_qids = [];
    // ---
    if (!empty($sql_td_qids)) return $sql_td_qids;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'qids']);
    } else {
        $query = "SELECT title, qid FROM qids";
        //---
        $data = fetch_query($query);
    }
    // ---
    $sql_td_qids = array_column($data, 'qid', 'title');
    // ---
    return $sql_td_qids;
}

function get_td_or_sql_users_no_inprocess()
{
    // ---
    global $from_api;
    // ---
    static $users = [];
    // ---
    if (!empty($users)) return $users;
    // ---
    if ($from_api) {
        $users = get_td_api(['get' => 'users_no_inprocess']);
    } else {
        $query = "SELECT * FROM users_no_inprocess order by id";
        //---
        $users = fetch_query($query);
    }
    // ---
    return $users;
}

function get_td_or_sql_full_translators()
{
    // ---
    global $from_api;
    // ---
    static $full_tr = [];
    // ---
    if (!empty($full_tr)) return $full_tr;
    // ---
    if ($from_api) {
        $full_tr = get_td_api(['get' => 'full_translators']);
    } else {
        $query = "SELECT * FROM full_translators";
        //---
        $full_tr = fetch_query($query);
    }
    // ---
    return $full_tr;
}
function get_td_or_sql_translate_type()
{
    // ---
    global $from_api;
    // ---
    static $translate_type = [];
    // ---
    if (!empty($translate_type ?? [])) {
        return $translate_type;
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'translate_type']);
    } else {
        $query = "SELECT tt_title, tt_lead, tt_full FROM translate_type";
        //---
        $data = fetch_query($query);
    }
    // ---
    $translate_type = $data;
    // ---
    return $data;
}

function get_td_or_sql_users_by_wiki()
{
    // ---
    global $from_api;
    // ---
    static $users_by_wiki = [];
    // ---
    if (!empty($users_by_wiki ?? [])) {
        return $users_by_wiki;
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'users_by_wiki']);
    } else {
        $query = <<<SQL
            SELECT user, lang, MAX(target_count) AS max_target, sum(target_count) AS sum_target
                FROM (
                    SELECT user, lang, COUNT(target) AS target_count
                    FROM pages
                    GROUP BY user, lang
                    ORDER BY 1 DESC
                ) AS subquery
            GROUP BY user
            ORDER BY 3 DESC
        SQL;
        //---
        $data = fetch_query($query);
    }
    // ---
    $users_by_wiki = $data;
    // ---
    return $data;
}

function get_td_or_sql_count_pages()
{
    // ---
    global $from_api;
    // ---
    static $count_pages = [];
    // ---
    if (!empty($count_pages ?? [])) {
        return $count_pages;
    }
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'count_pages']);
    } else {
        $query = <<<SQL
            SELECT DISTINCT user, count(target) as count from pages group by user order by count desc
        SQL;
        //---
        $data = fetch_query($query);
    }
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
