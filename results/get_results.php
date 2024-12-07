<?php

namespace Results\GetResults;

/*
Usage:

use function Results\GetResults\get_results;
use function Results\GetResults\get_cat_exists_and_missing;

*/

use function Results\GetCats\get_in_process;
use function Results\GetCats\get_mdwiki_cat_members;
use function Results\GetCats\open_json_file;
use function Actions\Functions\test_print;

function get_cat_exists_and_missing($cat, $camp, $depth, $code, $use_cache = true)
{
    $members_to = get_mdwiki_cat_members($cat, $use_cache = $use_cache, $depth = $depth, $camp = $camp);
    // z("<br>members_to size:" . count($members_to));
    $members = array();
    foreach ($members_to as $mr) {
        $members[] = $mr;
    };
    test_print("members size:" . count($members));
    $json_file = __DIR__ . "/../Tables/cash_exists/$code.json";

    $exists = open_json_file($json_file);

    test_print("$json_file: exists size:" . count($exists));

    // Find missing elements
    // $missing = array_diff($members, $exists);
    $missing = array();
    foreach ($members as $mem) {
        if (!in_array($mem, $exists)) $missing[] = $mem;
    };

    // Remove duplicates from $missing
    $missing = array_unique($missing);

    // Calculate length of exists
    $exs_len = count($members) - count($missing);

    $results = array(
        "len_of_exists" => $exs_len,
        "missing" => $missing
    );
    test_print("end of get_cat_exists_and_missing <br>===============================");
    return $results;
}


function get_results($cat, $camp, $depth, $code)
{
    //---
    $items = get_cat_exists_and_missing($cat, $camp, $depth, $code); # mdwiki pages in the cat
    //---
    if ($items == null) $items = array();
    //---
    $len_of_exists_pages = $items['len_of_exists'];
    $items_missing       = $items['missing'];
    //---
    $cat2 = $camps_cat2[$camp] ?? '';
    //---
    test_print("items_missing:" . count($items_missing) . "");
    //---
    if (!empty($cat2) && $cat2 != $cat) {
        $cat2_members = get_mdwiki_cat_members($cat2, $use_cache = true, $depth = $depth, $camp = $camp);
        $items_missing2 = array_intersect($items_missing, $cat2_members);
        test_print("items_missing2:" . count($items_missing2) . "");
        $items_missing = $items_missing2;
    }
    //---
    test_print("len_of_exists_pages: $len_of_exists_pages");
    //---
    $missing = array_unique($items_missing);
    //---
    $in_process = get_in_process($missing, $code);
    //---
    $len_in_process = count($in_process);
    //---
    $len_of_missing_pages = count($missing);
    $len_of_all           = $len_of_exists_pages + $len_of_missing_pages;
    //---
    $cat2 = "Category:" . str_replace('Category:', '', $cat);
    $caturl = "<a href='https://mdwiki.org/wiki/$cat2'>category</a>";
    //---
    $ix =  "Found $len_of_all pages in $caturl, $len_of_exists_pages exists, and $len_of_missing_pages missing in (<a href='https://$code.wikipedia.org'>$code</a>), $len_in_process In process.";
    //---s
    // delete $in_process keys from $missing
    if ($len_in_process > 0) {
        $missing = array_diff($missing, array_keys($in_process));
    };
    //---
    $tab = array(
        "in_process" => $in_process,
        "missing" => $missing,
        "ix" => $ix,
    );
    //---
    return $tab;
}
