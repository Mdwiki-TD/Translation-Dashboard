<?php

namespace Results\ResultsHelps;

/*
Usage:

use function Results\ResultsHelps\get_lang_exists_pages_from_cache;
use function Results\ResultsHelps\open_json_file;
use function Results\ResultsHelps\make_exists_targets;
use function Results\ResultsHelps\filter_items_missing_cat2;
use function Results\ResultsHelps\create_summary;

*/

use function TD\Render\TestPrint\test_print;
use function Tables\TablesDir\open_td_Tables_file;
use function SQLorAPI\Funcs\exists_by_qids_query;
use function Results\GetCats\get_mdwiki_cat_members;
use function TD\Render\Html\make_mdwiki_cat_url;

function open_json_file($file_path)
{
    if (!is_file($file_path)) {
        test_print("file $file_path does not exist");
        return [];
    }

    $text = file_get_contents($file_path);
    if ($text === false) {
        test_print("Failed to read file contents from $file_path");
        return [];
    }

    $data = json_decode($text, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        test_print("Failed to decode JSON from $file_path");
        return [];
    }

    return $data;
}

function get_lang_exists_pages_from_cache($code)
{
    // example of result like: [ "Spontaneous bacterial peritonitis", "Dronedarone", ... ]
    // ---
    $json_file = "cash_exists/$code.json";
    $exists = open_td_Tables_file($json_file);

    return $exists;
}


function make_exists_targets($targets_via_td, $exists, $code, $cat)
{
    //---
    // $targets_via_td = array_column($targets_via_td, 'target', 'title');
    $targets_via_td = array_column($targets_via_td, null, 'title');
    // { "id": 6982, "title": "Video:Abdominal thrusts", "word": 117, "translate_type": "all", "cat": "RTTVideo", "lang": "ar", "user": "Mr. Ibrahem", "target": "ويكيبيديا:فيديوويكي\/ضغطات البطن", "date": "2025-02-06", "pupdate": "2025-02-06", "add_date": "2025-02-06 03:00:00", "deleted": 0, "mdwiki_revid": null }
    //---
    $exists_targets_before = exists_by_qids_query($code, $cat);
    // $exists_targets_before = array_column($exists_targets_before, 'target', 'title');
    $exists_targets_before = array_column($exists_targets_before, null, 'title');
    // { "qid": "Q133005500", "title": "Video:Abdominal thrusts", "category": "RTTVideo", "code": "ar", "target": "ويكيبيديا:فيديوويكي\/ضغطات البطن" }
    //---
    $tab = [];
    //---
    foreach ($exists as $_ => $title) {
        $td_link = $targets_via_td[$title] ?? [];
        $before_link = $exists_targets_before[$title] ?? [];
        // ---
        $one_tab = [];
        // ---
        $one_tab["qid"] = $td_link['qid'] ?? $before_link['qid'] ?? "";
        $one_tab["title"] = $td_link['title'] ?? $before_link['title'] ?? "";
        // ---
        $one_tab["target"] = $td_link['target'] ?? $before_link['target'] ?? "";
        // ---
        // ---
        if ($td_link['target'] ?? "") {
            $one_tab["via"] = "td";
            // ---
            $one_tab["pupdate"] = $td_link['pupdate'] ?? "";
            $one_tab["user"] = $td_link['user'] ?? "";
            // ---
        } elseif ($before_link['target'] ?? "") {
            $one_tab["via"] = "before";
        } else {
            $one_tab["via"] = "none";
        }
        // ---
        $tab[$title] = $one_tab;
    }
    //---
    return $tab;
}


function filter_items_missing_cat2($items_missing, $cat2, $depth)
{
    // ---
    // $cat2_members = get_mdwiki_cat_members($cat2, $use_cache = true, $depth = $depth, $camp = $camp);
    // ---
    $cat2_members = get_mdwiki_cat_members($cat2, $depth, true);
    // ---
    $items_missing = array_intersect($items_missing, $cat2_members);
    test_print("Items missing after intersecting with cat2: " . count($items_missing));
    // ---
    return $items_missing;
}

function create_summary($code, $cat, $len_inprocess, $len_missing, $len_of_exists_pages)
{

    $len_of_all = $len_of_exists_pages + $len_missing + $len_inprocess;

    // Prepare category URL
    $caturl = make_mdwiki_cat_url($cat, "Category");

    // Generate summary message
    $summary = "Found $len_of_all pages in $caturl, $len_of_exists_pages exists, and $len_missing missing in (<a href='https://$code.wikipedia.org' target='_blank'>$code</a>), $len_inprocess In process.";
    // ---
    return $summary;
}
