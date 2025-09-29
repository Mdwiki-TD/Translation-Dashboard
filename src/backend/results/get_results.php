<?php

namespace Results\GetResults;

/*
Usage:

use function Results\GetResults\get_results; // get_results($cat, $camp, $depth, $code)

*/

use Tables\SqlTables\TablesSql;
use function Results\FetchCatData\get_cat_exists_and_missing;
use function Results\SparqlBot\filter_existing_out;
use function TD\Render\TestPrint\test_print;
use function SQLorAPI\Process\get_lang_in_process_new;
use function SQLorAPI\Funcs\get_lang_pages_by_cat;
use function Results\ResultsHelps\make_exists_targets;
use function Results\ResultsHelps\filter_items_missing_cat2;
use function Results\ResultsHelps\create_summary;


function getinprocess($missing, $code)
{
    $res = get_lang_in_process_new($code);
    $titles = [];
    foreach ($res as $t) {
        if (in_array($t['title'], $missing)) $titles[$t['title']] = $t;
    }
    return $titles;
}

function get_results($cat, $camp, $depth, $code, $filter_sparql): array
{
    // Get existing and missing pages
    // ---
    $targets_via_td = get_lang_pages_by_cat($code, $cat);
    //---
    $items = get_cat_exists_and_missing($cat, $depth, $code, true);
    // ---
    $items_missing = $items['missing'];
    $items_exists = $items['exists'];
    // ---
    if (!empty($filter_sparql)) {
        [$items_exists, $items_missing] = filter_existing_out($items_missing, $items_exists, $code);
    }
    // ---
    $items_exists = make_exists_targets($targets_via_td, $items_exists, $code, $cat);
    // ---
    test_print("Items missing: " . count($items_missing));

    // Check for a secondary category
    $cat2 = TablesSql::$s_camps_cat2[$camp] ?? '';

    if (!empty($cat2) && $cat2 !== $cat) {
        $items_missing = filter_items_missing_cat2($items_missing, $cat2, $depth);
    }

    $len_of_exists_pages = count($items_exists);

    test_print("Length of existing pages: $len_of_exists_pages");

    // Remove duplicates from missing items
    $missing = array_unique($items_missing);

    // Get in-process items
    $inprocess = getinprocess($missing, $code);
    $len_inprocess = count($inprocess);

    // Remove in-process items from missing list
    if ($len_inprocess > 0) {
        $inprocess_2 = array_column($inprocess, 'title');
        $missing = array_diff($missing, $inprocess_2);
    }

    $summary = create_summary($code, $cat, count($inprocess), count($missing), $len_of_exists_pages);

    return [
        "inprocess" => $inprocess,
        "exists" => $items_exists,
        "missing" => $missing,
        "ix" => $summary,
    ];
}
