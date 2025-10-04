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
    $members = get_mdwiki_cat_members($cat, $depth, $use_cache);
    // ---
    test_print("get_cat_exists and_missing Members size: " . count($members));

    $exists = get_lang_exists_pages_from_cache($code);
    // ---
    // pages that exist in $exists and $members
    $exists = array_intersect($members, $exists);
    // ---
    // change from ("{"6":"Video:Cancer"}") to (["Video:Cancer"])
    $exists = array_values($exists);
    // ---
    // Find missing members
    $missing = array_diff($members, $exists);
    // ---
    $missing = array_values(array_unique($missing));
    // ---
    // test_print("End of get_cat exists_and_missing <br>===============================");
    return [$exists, $missing];
}
