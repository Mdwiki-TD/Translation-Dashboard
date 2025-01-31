<?php

namespace Results\FetchCatData;

/*
Usage:

use function Results\FetchCatData\get_cat_exists_and_missing;

*/

use function Results\GetCats\get_mdwiki_cat_members;
use function Actions\Functions\test_print;
use function Results\ResultsHelps\print_r_it;
use function Results\ResultsHelps\get_lang_exists_pages;

function get_cat_exists_and_missing($cat, $camp, $depth, $code, $use_cache = true): array
{
    // Fetch category members
    $members_to = get_mdwiki_cat_members($cat, $use_cache = $use_cache, $depth = $depth, $camp = $camp);

    print_r_it($members_to, 'members_to');

    $members = array_values($members_to); // Flatten the array

    print_r_it($members, 'members');

    test_print("Members size: " . count($members));

    $exists = get_lang_exists_pages($code);

    // Find missing members
    $missing = array_diff($members, $exists);

    $missing = array_unique($missing);

    // Calculate the length of existing pages
    $exs_len = count($members) - count($missing);

    test_print("End of get_cat_exists_and_missing <br>===============================");

    return [
        "len_of_exists" => $exs_len,
        "missing" => $missing,
    ];
}
