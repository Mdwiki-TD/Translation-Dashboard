<?php

namespace Results\FetchCatDataSparql;

/*
Usage:

use function Results\FetchCatDataSparql\get_cat_exists_and_missing;

*/

use function Results\GetCats\get_mdwiki_cat_members;
use function Actions\TestPrint\test_print;
use function Results\ResultsHelps\get_lang_exists_pages;
use function Results\SparqlBot\filter_existing_out;

function get_cat_exists_and_missing($cat, $depth, $code, $use_cache = true): array
{
    // Fetch category members
    // $members = get_mdwiki_cat_members($cat, $use_cache = $use_cache, $depth = $depth, $camp = $camp);
    $members = get_mdwiki_cat_members($cat, $depth, $use_cache);

    test_print("Members size: " . count($members));

    $exists = get_lang_exists_pages($code);
    // ---
    // pages that exist in $exists and $members
    $exists = array_intersect($members, $exists);
    $exists = array_values($exists);
    // ---
    // var_dump($exists);
    // ---
    // Find missing members
    $missing = array_diff($members, $exists);

    $missing = array_unique($missing);

    $missing = filter_existing_out($missing, $code);

    // Calculate the length of existing pages
    $exs_len = count($members) - count($missing);

    // test_print("End of get_cat_exists_and_missing <br>===============================");

    return [
        "len_of_exists" => $exs_len,
        "missing" => $missing,
        "exists" => $exists,
    ];
}
