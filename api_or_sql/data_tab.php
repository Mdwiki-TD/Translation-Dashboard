<?php

namespace SQLorAPI\GetDataTab;

/*

Usage:

use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;
use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function SQLorAPI\GetDataTab\get_td_or_sql_categories;
use function SQLorAPI\GetDataTab\get_td_or_sql_projects;
use function SQLorAPI\GetDataTab\get_td_or_sql_settings;
use function SQLorAPI\GetDataTab\get_td_or_sql_views;
use function SQLorAPI\GetDataTab\get_td_or_sql_titles_infos;

*/

use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;

$settings_tabe = array_column(get_td_api(['get' => 'settings']), 'value', 'title');
//---
$from_api  = ($settings_tabe['use_td_api'] ?? "" == "1") ? true : false;

function isvalid($str)
{
    return !empty($str) && $str != 'All' && $str != 'all';
}

function get_td_or_sql_titles_infos()
{
    // ---
    global $from_api;
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
            JOIN enwiki_pageviews ep ON ase.title = ep.title
            JOIN qids q ON q.title = ase.title
            JOIN refs_counts rc ON rc.r_title = ase.title
            JOIN words w ON w.w_title = ase.title
        SQL;
        // ---
        $data = fetch_query($qua);
    }
    // ---
    return $data;
}

function get_td_or_sql_views()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'views']);
    } else {
        $query = "SELECT target, lang, countall, count2021, count2022, count2023, count2024, count2025, count2026 FROM views";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_td_or_sql_settings()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'settings']);
    } else {
        $query = "select id, title, displayed, value, Type from settings";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_td_or_sql_projects()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'projects']);
    } else {
        $query = "select g_id, g_title from projects";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_td_or_sql_categories()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'categories']);
    } else {
        $query = "select id, category, category2, campaign, depth, def from categories";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_td_or_sql_qids()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'qids']);
    } else {
        $query = "SELECT title, qid FROM qids";
        //---
        $data = fetch_query($query);
    }
    // ---
    $t_qids = array_column($data, 'qid', 'title');
    //---
    return $t_qids;
}
function get_td_or_sql_full_translators()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'full_translators']);
    } else {
        $query = "SELECT * FROM full_translators";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
function get_td_or_sql_translate_type()
{
    // ---
    global $from_api;
    // ---
    if ($from_api) {
        $data = get_td_api(['get' => 'translate_type']);
    } else {
        $query = "SELECT tt_title, tt_lead, tt_full FROM translate_type";
        //---
        $data = fetch_query($query);
    }
    // ---
    return $data;
}
