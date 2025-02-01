<?php

namespace Results\FetchCatDataSparql;

/*
Usage:

use function Results\FetchCatDataSparql\get_cat_exists_and_missing;

*/

use function Results\GetCats\get_mdwiki_cat_members;
use function Actions\Functions\test_print;
use function Results\ResultsHelps\get_lang_exists_pages;
use function Results\SparqlBot\check_missing;

function get_cat_exists_and_missing($cat, $camp, $depth, $code, $use_cache = true): array
{
    // Fetch category members
    $members = get_mdwiki_cat_members($cat, $use_cache = $use_cache, $depth = $depth, $camp = $camp);

    test_print("Members size: " . count($members));

    $exists = get_lang_exists_pages($code);
    // ---
    // pages that exist in $exists and $members
    $exists = array_intersect($members, $exists);
    // ---

    // Find missing members
    $missing = array_diff($members, $exists);

    $missing = array_unique($missing);

    $missing = check_missing($missing, $code);
    $missing = array_values($missing);

    // Calculate the length of existing pages
    $exs_len = count($members) - count($missing);

    test_print("End of get_cat_exists_and_missing <br>===============================");

    return [
        "len_of_exists" => $exs_len,
        "missing" => $missing,
    ];
}
