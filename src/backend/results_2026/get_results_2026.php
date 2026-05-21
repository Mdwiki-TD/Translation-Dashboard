<?php

namespace Results\GetResults2026;

/*
Usage:

use function Results\GetResults2026\get_results_2026; // get_results_2026($cat, $camp, $depth, $code)

*/

use function Results\ResultsHelps\create_summary;
use function SQLorAPI\Process\get_lang_in_process;
use function SQLorAPI\Funcs\get_lang_pages_by_cat;
use function Results\GetCats\get_mdwiki_cat_members;

use function SQLorAPI\Funcs\missing_by_lang_and_category;
use function SQLorAPI\Funcs\exists_by_lang_and_category;

use function TD\Render\TestPrint\test_print;

function get_cat_exists_and_missing_new($exists_via_td, $cat, $depth, $code, $use_cache = true): array
{
    // ---
    // Fetch category members
    $members = get_mdwiki_cat_members($cat, $depth, $use_cache);
    // ---
    test_print("get_cat_exists and_missing Members size: " . count($members));
    // ---
    // pages that exist in $exists and $members
    // ---
    $exists = array_filter($exists_via_td, function ($item) use ($members) {
        return in_array($item['title'], $members);
    });
    // ---
    $func = function ($item) {
        return [
            "qid" => $item["qid"] ?? "",
            "title" => $item["title"] ?? "",
            "target" => $item["target"] ?? "",
            "via" => "td",
            "pupdate" => $item["pupdate"] ?? "",
            "user" => $item["user"] ?? ""
        ];
    };
    // ---
    $exists = array_map($func, $exists);
    // ---
    // Find missing members
    $missing = array_diff($members, array_keys($exists));
    // ---
    $missing = array_values(array_unique($missing));
    // ---
    // test_print("End of get_cat exists_and_missing <br>===============================");
    return [$exists, $missing];
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
    $res = get_lang_in_process($code);
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

function get_results_2026($cat, $code): array
{
    // Get existing and missing pages
    // ---
    $exists_via_td = get_lang_pages_by_cat($code, $cat);
    $exists_via_td = array_column($exists_via_td, null, "title");
    test_print("exists_via_td " . count($exists_via_td));
    // ---
    // { "title": "Alpha-gal syndrome", "category": "RTT", "importance": "Mid", "r_lead_refs": 0, "r_all_refs": 0, "en_views": 15, "w_lead_words": 0, "w_all_words": 0, "qid": "Q16242785" }
    $items_missing = missing_by_lang_and_category($code, $cat);
    // $items_missing = array_column($items_missing, "title");
    // --
    // { "title": "11p deletion syndrome", "category": "RTT", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 838, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153", "target": "متلازمة واجر" }
    $items_exists  = exists_by_lang_and_category($code, $cat);
    $items_exists = array_column($items_exists, null, "title");
    // ---
    // add column to all $items_exists ("via" => "before") or ("via" => "td") if title in $exists_via_td
    foreach ($items_exists as $title => &$_item) {
        if (isset($exists_via_td[$title])) {
            $_item['via'] = 'td';
        } else {
            $_item['via'] = 'before';
        }
    }
    unset($_item);
    // ---
    test_print(">>>> Items missing " . count($items_missing));
    test_print(">>>> Items exists " . count($items_exists));
    // ---

    $len_of_exists_pages = count($items_exists);
    test_print("Length of existing pages: $len_of_exists_pages");

    // Get in-process items
    $missing_keys = array_column($items_missing, "title");
    $inprocess = getinprocess_n($missing_keys, $code);

    $missing = $items_missing;

    // Remove in-process items from missing list
    if (count($inprocess) > 0) {
        $inprocess_titles = array_flip(array_column($inprocess, 'title'));

        $missing = array_filter($missing, function ($item) use ($inprocess_titles) {
            return !isset($inprocess_titles[$item['title']]);
        });
    }

    $summary = create_summary($code, $cat, count($inprocess), count($missing), $len_of_exists_pages);

    // sort $items_exists by keys
    ksort($items_exists);

    return [
        "ix" => $summary,
        "inprocess" => $inprocess,
        "exists" => $items_exists,
        "missing" => $missing,
    ];
}
