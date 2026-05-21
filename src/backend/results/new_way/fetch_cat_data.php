<?php

namespace Results\FetchCatDataNew;

/*
Usage:

use function Results\FetchCatDataNew\get_cat_exists_and_missing_new;

*/

use function Results\GetCats\get_mdwiki_cat_members;
use function TD\Render\TestPrint\test_print;

function _get_cat_exists_and_missing_new($exists_via_td, $cat, $depth, $code, $use_cache = true): array
{
    /*
        Performance: Using in_array inside array_filter results in O(N*M) complexity. Using a lookup table (via array_flip) is much more efficient.
    */
    // Fetch category members
    $members = get_mdwiki_cat_members($cat, $depth, $use_cache);

    test_print("get_cat_exists and_missing Members size: " . count($members));

    // pages that exist in $exists and $members

    $exists = array_filter($exists_via_td, function ($item) use ($members) {
        return in_array($item['title'], $members);
    });

    $func = function ($item) {
        $title = $item['title'] ?? '';
        return [
            "qid" => $item["qid"] ?? "",
            "title" => $title,
            "target" => $item["target"] ?? "",
            "via" => "td",
            "pupdate" => $item["pupdate"] ?? "",
            "user" => $item["user"] ?? ""
        ];
    };

    $exists = array_map($func, $exists);

    // Find missing members
    $missing = array_diff($members, array_keys($exists));
    $missing = array_values(array_unique($missing));

    // test_print("End of get_cat exists_and_missing <br>===============================");
    return [$exists, $missing];
}


function get_cat_exists_and_missing_new($exists_via_td, $cat, $depth, $code, $use_cache = true): array
{

    // Fetch category members
    $members = get_mdwiki_cat_members($cat, $depth, $use_cache);

    test_print("get_cat_exists and_missing Members size: " . count($members));

    // pages that exist in $exists and $members

    $members_lookup = array_flip($members);

    $exists = [];
    foreach ($exists_via_td as $item) {
        $title = $item['title'] ?? '';
        if (isset($members_lookup[$title])) {
            $exists[$title] = [
                "qid" => $item["qid"] ?? "",
                "title" => $title,
                "target" => $item["target"] ?? "",
                "via" => "td",
                "pupdate" => $item["pupdate"] ?? "",
                "user" => $item["user"] ?? ""
            ];
        }
    }

    // Find missing members
    $missing = array_diff($members, array_keys($exists));
    $missing = array_values(array_unique($missing));

    // test_print("End of get_cat exists_and_missing <br>===============================");
    return [$exists, $missing];
}
