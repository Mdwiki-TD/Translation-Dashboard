<?php

namespace Results\FetchCatData;

/*
Usage:

use function Results\FetchCatData\get_cat_exists_and_missing;

*/

use function Results\GetCats\get_mdwiki_cat_members;
use function Results\GetCats\open_json_file;
use function Actions\Functions\test_print;

$test11 = $_GET['test11'] ?? '';

function print_r_it($data, $title)
{
    if (empty($test11)) return;

    echo "$title:" . count($data) . "<br>";
    echo "<pre>";
    // print(json_encode($data));
    echo "</pre>";
}

function get_cat_exists_and_missing($cat, $camp, $depth, $code, $use_cache = true): array
{
    // Fetch category members
    $members_to = get_mdwiki_cat_members($cat, $use_cache = $use_cache, $depth = $depth, $camp = $camp);

    print_r_it($members_to, 'members_to');

    $members = array_values($members_to); // Flatten the array

    print_r_it($members, 'members');

    test_print("Members size: " . count($members));

    // Determine the directory for JSON files
    $tables_dir = getenv('tables_dir') ?: __DIR__ . '/../../td/Tables';
    if (substr($tables_dir, 0, 2) === 'I:') {
        $tables_dir = 'I:/mdwiki/mdwiki/public_html/td/Tables';
    }

    // Load existing pages from JSON file
    $json_file = "$tables_dir/cash_exists/$code.json";
    $exists = open_json_file($json_file);
    test_print("$json_file: Exists size: " . count($exists));

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
