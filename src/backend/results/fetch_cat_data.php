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
    // var_dump($exists);
    // ---
    // Find missing members
    $missing = array_diff($members, $exists);

    $missing = array_unique($missing);

    // ---
    // Calculate the length of existing pages
    $exs_len = count($members) - count($missing);

    // test_print("End of get_cat exists_and_missing <br>===============================");

    return [
        "len_of_exists" => $exs_len,
        "missing" => $missing,
        "exists" => $exists,
    ];
}
