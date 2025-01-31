<?php

namespace Results\FetchCatDataSparql;

/*
Usage:

use function Results\FetchCatDataSparql\get_cat_exists_and_missing;

*/

use function Results\GetCats\get_mdwiki_cat_members;
use function Results\GetCats\open_json_file;
use function Actions\Functions\test_print;
use function SQLorAPI\GetDataTab\get_td_or_sql_qids;

$test11 = $_GET['test11'] ?? '';

function print_r_it($data, $title, $d = false)
{
    if (empty($test11)) return;

    echo "$title:" . count($data) . "<br>";
    echo "<pre>";
    if ($d) {
        print(json_encode($data));
    }
    echo "</pre>";
}

function get_cat_exists_and_missing($cat, $camp, $depth, $code, $use_cache = true): array
{
    //---
    $sql_qids = array();
    //---
    print_r_it($sql_qids, "sql_qids", 1);
    //---
    $qids_t = get_td_or_sql_qids();
    //---
    foreach ($qids_t as $k => $tab) $sql_qids[$tab['title']] = $tab['qid'];
    //---
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
