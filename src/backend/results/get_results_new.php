<?php

namespace Results\GetResults;

/*
Usage:

use function Results\GetResults\get_results_new; // get_results_new($cat, $camp, $depth, $code)

*/

use Tables\SqlTables\TablesSql;
use function Results\FetchCatData\get_cat_exists_and_missing_new;
use function Results\SparqlBot\filter_existing_out_new;
use function TD\Render\TestPrint\test_print;
use function SQLorAPI\Process\get_lang_in_process_new;
use function SQLorAPI\Funcs\get_lang_pages_by_cat;
use function Results\ResultsHelps\filter_items_missing_cat2;
use function Results\ResultsHelps\create_summary;
use function SQLorAPI\Funcs\exists_by_qids_query;


function make_exists_targets_new($exists, $code)
{
    //---
    $exists_targets_before = exists_by_qids_query($code);
    $exists_targets_before = array_column($exists_targets_before, null, 'title');
    // { "qid": "Q133005500", "title": "Video:Abdominal thrusts", "category": "RTTVideo", "code": "ar", "target": "ويكيبيديا:فيديوويكي\/ضغطات البطن" }
    //---
    $tab = [];
    //---
    foreach ($exists as $_ => $title) {
        $td_link = [];
        $before_link = $exists_targets_before[$title] ?? [];
        // ---
        $one_tab = [];
        // ---
        $one_tab["qid"] = $td_link['qid'] ?? $before_link['qid'] ?? "";
        $one_tab["title"] = $td_link['title'] ?? $before_link['title'] ?? "";
        // ---
        $one_tab["target"] = $td_link['target'] ?? $before_link['target'] ?? "";
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

function getinprocess_n($missing, $code)
{
    $res = get_lang_in_process_new($code);
    $titles = [];
    foreach ($res as $t) {
        if (in_array($t['title'], $missing)) $titles[$t['title']] = $t;
    }
    return $titles;
}

function get_results_new($cat, $camp, $depth, $code, $filter_sparql): array
{
    // Get existing and missing pages
    // ---
    $exists_via_td = get_lang_pages_by_cat($code, $cat);
    // { "id": 6982, "title": "Video:Abdominal thrusts", "word": 117, "translate_type": "all", "cat": "RTTVideo", "lang": "ar", "user": "Mr. Ibrahem", "target": "ويكيبيديا:فيديوويكي\/ضغطات البطن", "date": "2025-02-06", "pupdate": "2025-02-06", "add_date": "2025-02-06 03:00:00", "deleted": 0, "mdwiki_revid": null }
    //---
    $exists_via_td = array_column($exists_via_td, null, "title");
    //---
    $items = get_cat_exists_and_missing_new($exists_via_td, $cat, $depth, $code, true);
    // ---
    $items_missing = $items['missing'];
    $items_exists = $items['exists'];
    // ---
    if (!empty($filter_sparql)) {
        [$items_exists_2, $items_missing] = filter_existing_out_new($items_missing, $code);
        // ---
        $exists_add = make_exists_targets_new($items_exists_2, $code);
        // ---
        $items_exists = array_merge($items_exists, $exists_add);
    }
    // ---
    test_print("Items missing: " . count($items_missing));

    // Check for a secondary category
    $cat2 = TablesSql::$s_camps_cat2[$camp] ?? '';

    if (!empty($cat2) && $cat2 !== $cat) {
        $items_missing = filter_items_missing_cat2($items_missing, $cat2, $depth);
    }

    $len_of_exists_pages = count($items_exists);
    test_print("Length of existing pages: $len_of_exists_pages");

    // Remove duplicates from missing items
    $missing = array_unique($items_missing);

    // Get in-process items
    $inprocess = getinprocess_n($missing, $code);
    $len_inprocess = count($inprocess);

    // Remove in-process items from missing list
    if ($len_inprocess > 0) {
        $inprocess_2 = array_column($inprocess, 'title');
        $missing = array_diff($missing, $inprocess_2);
    }

    $summary = create_summary($code, $cat, count($inprocess), count($missing), $len_of_exists_pages);

    return [
        "inprocess" => $inprocess,
        "exists" => $items_exists,
        "missing" => $missing,
        "ix" => $summary,
    ];
}
