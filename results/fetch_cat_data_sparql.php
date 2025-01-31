<?php

namespace Results\FetchCatDataSparql;

/*
Usage:

use function Results\FetchCatDataSparql\get_cat_exists_and_missing;

*/

use function Results\GetCats\get_mdwiki_cat_members;
use function Actions\Functions\test_print;
use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function Results\ResultsHelps\print_r_it;
use function Results\ResultsHelps\get_lang_exists_pages;

function get_qids($list)
{
    //---
    $sql_qids = get_td_or_sql_qids();
    //---
    $with_qids = [];
    $no_qids = [];
    // ---
    foreach ($list as $member) {
        $qid = $sql_qids[$member] ?? 0;
        if ($qid) {
            $with_qids[$member] = $qid;
        } else {
            $no_qids[] = $member;
        }
    }
    // ---
    print_r_it($with_qids, "with_qids", 1);
    print_r_it($no_qids, "no_qids", 1);
    // ---
    return [
        "with_qids" => $with_qids,
        "no_qids" => $no_qids,
    ];
}
function get_cat_exists_and_missing($cat, $camp, $depth, $code, $use_cache = true): array
{
    //---
    $members = get_mdwiki_cat_members($cat, $use_cache = $use_cache, $depth = $depth, $camp = $camp);
    //---
    print_r_it($members, 'members', $d = 1);
    test_print("Members size: " . count($members));
    // ---
    $qids_tab = get_qids($members);
    //---
    $with_qids = $qids_tab['with_qids'];
    $no_qids = $qids_tab['no_qids'];
    //---
    $exists = get_lang_exists_pages($code);

    // Find missing members
    $missing = array_diff($members, $exists);

    $missing = array_unique($missing);

    // Calculate the length of existing pages
    $exs_len = count($members) - count($missing);

    test_print("End of get_cat_exists_and_missing <br>===============================");

    // ---
    echo "len_of_exists: " . $exs_len . "<br>";
    // ---
    print_r_it($missing, 'missing', $r = 1);
    // ---
    return [
        "len_of_exists" => $exs_len,
        "missing" => $missing,
    ];
}
