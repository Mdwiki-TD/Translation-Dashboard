<?php

namespace Results\FetchCatData;

/*
Usage:

use function Results\FetchCatData\get_cat_exists_and_missing;

*/

use function Results\GetCats\get_mdwiki_cat_members;
use function TD\Render\TestPrint\test_print;
use function Results\ResultsHelps\get_lang_exists_pages_from_cache;

function get_cat_exists_and_missing($cat, $depth, $code, $use_cache = true): array
{
    // Fetch category members
    // $members = get_mdwiki_cat_members($cat, $use_cache = $use_cache, $depth = $depth, $camp = $camp);
    $members = get_mdwiki_cat_members($cat, $depth, $use_cache);

    test_print("get_cat_exists and_missing Members size: " . count($members));

    $exists = get_lang_exists_pages_from_cache($code);
    // ---
    // pages that exist in $exists and $members
    $exists = array_intersect($members, $exists);
    // ---
    // change from ("{"6":"Video:Cancer"}") to (["Video:Cancer"])
    // $exists = array_values($exists);
    // ---
    // Find missing members
    $missing = array_diff($members, $exists);

    $missing = array_unique($missing);
    // ---
    // ---
    // test_print("End of get_cat exists_and_missing <br>===============================");

    return [
        "missing" => $missing,
        "exists" => $exists,
    ];
}

function get_cat_exists_and_missing_new($exists_via_td, $cat, $depth, $code, $use_cache = true): array
{
    // $exists_via_td example:
    // { "id": 15, "title": "Kidney dysplasia", "word": 180, "translate_type": "lead", "cat": "RTT", "lang": "ar", "user": "عرين أسد أبو رمان", "target": "خلل التنسج الكلوي", "date": "2021-07-10", "pupdate": "2021-07-10", "add_date": "2021-07-10 03:00:00", "deleted": 0, "mdwiki_revid": null }
    // ---
    $members = get_mdwiki_cat_members($cat, $depth, $use_cache);
    test_print("get_cat_exists and_missing Members size: " . count($members));
    // ---
    // $exists = get_lang_exists_pages_from_cache($code);
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
    // change from ("{"6":"Video:Cancer"}") to (["Video:Cancer"])
    // $exists = array_values($exists);
    // ---
    // Find missing members
    $missing = array_diff($members, array_keys($exists));

    // var_export(json_encode($missing));
    // ---
    $missing = array_unique($missing);
    // ---
    // test_print("End of get_cat exists_and_missing <br>===============================");

    return [
        "missing" => $missing,
        "exists" => $exists,
    ];
}
