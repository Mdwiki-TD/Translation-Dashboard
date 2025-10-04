<?php

namespace Results\GetResults;

/*
Usage:

use function Results\GetResults\get_results_new; // get_results_new($cat, $camp, $depth, $code)

*/

use function Results\FetchCatDataNew\get_cat_exists_and_missing_new;
use function Results\SparqlBot\filter_existing_out_new;
use function TD\Render\TestPrint\test_print;
use function SQLorAPI\Process\get_lang_in_process_new;
use function SQLorAPI\Funcs\get_lang_pages_by_cat;
use function Results\ResultsHelps\filter_items_missing_cat2;
use function Results\ResultsHelps\create_summary;
use function SQLorAPI\Funcs\exists_by_qids_query;


function make_exists_targets_new($exists, $code, $exists_targets_before)
{
    //---
    // { "qid": "Q133005500", "title": "Video:Abdominal thrusts", "category": "RTTVideo", "code": "ar", "target": "ويكيبيديا:فيديوويكي\/ضغطات البطن" }
    //---
    $tab = [];
    //---
    foreach ($exists as $_ => $title) {
        $before_link = $exists_targets_before[$title] ?? [];
        // ---
        $one_tab = [];
        // ---
        $one_tab["qid"] = $before_link['qid'] ?? "";
        $one_tab["title"] = $before_link['title'] ?? "";
        // ---
        $one_tab["target"] = $before_link['target'] ?? "";
        // ---
        if ($before_link['target'] ?? "") {
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

function exists_expends($items_missing, $exists_targets_before)
{
    //---
    test_print("++ exists_expends:");
    test_print("++ before ++ items_missing " . count($items_missing));
    // ---
    // { "qid": "Q133005500", "title": "Video:Abdominal thrusts", "category": "RTTVideo", "code": "ar", "target": "ويكيبيديا:فيديوويكي\/ضغطات البطن" }
    //---
    $items_exists = [];
    $missing_new = [];
    //---
    foreach ($items_missing as $title) {
        // ---
        $before_link = $exists_targets_before[$title] ?? [];
        $target = $before_link['target'] ?? "";
        // ---
        if ($target) {
            // ---
            $items_exists[$title] = [
                "qid" => $before_link['qid'] ?? "",
                "title" => $before_link['title'] ?? "",
                "target" => $target,
                "via" => "before"
            ];
        } else {
            $missing_new[] = $title;
        }
    }
    //---
    test_print("++ after ++ items_exists " . count($items_exists));
    test_print("++ after ++ items_missing " . count($missing_new));
    //---
    return $items_exists;
}

function getinprocess_n($missing, $code)
{
    $res = get_lang_in_process_new($code);
    // ---
    $titles = [];
    // ---
    foreach ($res as $t) {
        if (in_array($t['title'], $missing)) {
            $titles[$t['title']] = $t;
        }
    }
    // ---
    return $titles;
}

function get_results_new($cat, $camp, $depth, $code, $filter_sparql, $cat2): array
{
    // Get existing and missing pages
    // ---
    $exists_via_td = get_lang_pages_by_cat($code, $cat);
    $exists_via_td = array_column($exists_via_td, null, "title");
    //---
    test_print("exists_via_td " . count($exists_via_td));
    // ---
    [$items_exists, $items_missing] = get_cat_exists_and_missing_new($exists_via_td, $cat, $depth, $code, true);
    // ---
    $exists_targets_before = exists_by_qids_query($code);
    $exists_targets_before = array_column($exists_targets_before, null, 'title');
    // ---
    test_print("Items missing before filter_sparql " . count($items_missing));
    // ---
    $exists_1 = exists_expends($items_missing, $exists_targets_before);
    // ---
    if ($exists_1) {
        // ---
        $items_missing = array_diff($items_missing, array_keys($exists_1));
        $items_exists = array_merge($items_exists, $exists_1);
    }
    // ---
    if (!empty($filter_sparql)) {
        // ---
        $items_exists_2 = filter_existing_out_new($items_missing, $code);
        // ---
        if ($items_exists_2) {
            // ---
            $items_missing = array_diff($items_missing, $items_exists_2);
            // ---
            $exists_add = make_exists_targets_new($items_exists_2, $code, $exists_targets_before);
            // ---
            $items_exists = array_merge($items_exists, $exists_add);
        }
    }
    // ---
    test_print("Items missing: " . count($items_missing));

    // Check for a secondary category

    if (!empty($cat2) && $cat2 !== $cat) {
        $items_missing = filter_items_missing_cat2($items_missing, $cat2, $depth);
    }

    $len_of_exists_pages = count($items_exists);
    test_print("Length of existing pages: $len_of_exists_pages");

    // Remove duplicates from missing items
    $missing = array_values(array_unique($items_missing));

    // Get in-process items
    $inprocess = getinprocess_n($missing, $code);
    $len_inprocess = count($inprocess);

    // Remove in-process items from missing list
    if ($len_inprocess > 0) {
        $missing = array_diff($missing, array_column($inprocess, 'title'));
        $missing = array_values($missing);
    }

    $summary = create_summary($code, $cat, count($inprocess), count($missing), $len_of_exists_pages);

    // sort $items_exists by keys
    ksort($items_exists);

    return [
        "inprocess" => $inprocess,
        "exists" => $items_exists,
        "missing" => $missing,
        "ix" => $summary,
    ];
}
