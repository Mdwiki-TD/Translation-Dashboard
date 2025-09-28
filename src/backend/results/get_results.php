<?php

namespace Results\GetResults;

/*
Usage:

use function Results\GetResults\get_results;

*/

use Tables\SqlTables\TablesSql;
// use function Results\FetchCatData\get_cat_exists_and_missing;
use function Results\FetchCatDataSparql\get_cat_exists_and_missing;
use function Results\GetCats\get_mdwiki_cat_members;
use function TD\Render\TestPrint\test_print;
use function SQLorAPI\Process\get_lang_in_process_new;
use function TD\Render\Html\make_mdwiki_cat_url;

function filter_items_missing_cat2($items_missing, $cat2, $depth)
{
    // ---
    // $cat2_members = get_mdwiki_cat_members($cat2, $use_cache = true, $depth = $depth, $camp = $camp);
    // ---
    $cat2_members = get_mdwiki_cat_members($cat2, $depth, true);
    // ---
    $items_missing = array_intersect($items_missing, $cat2_members);
    test_print("Items missing after intersecting with cat2: " . count($items_missing));
    // ---
    return $items_missing;
}

function create_summary($code, $cat, $inprocess, $missing, $len_of_exists_pages)
{

    $len_inprocess = count($inprocess);

    // Calculate totals
    $len_of_missing_pages = count($missing);
    $len_of_all = $len_of_exists_pages + $len_of_missing_pages;

    // Prepare category URL
    $caturl = make_mdwiki_cat_url($cat, "Category");

    // Generate summary message
    $summary = "Found $len_of_all pages in $caturl, $len_of_exists_pages exists, and $len_of_missing_pages missing in (<a href='https://$code.wikipedia.org' target='_blank'>$code</a>), $len_inprocess In process.";
    // ---
    return $summary;
}

function getinprocess($missing, $code)
{
    $res = get_lang_in_process_new($code);
    $titles = [];
    foreach ($res as $t) {
        if (in_array($t['title'], $missing)) $titles[$t['title']] = $t;
    }
    return $titles;
}

function get_results($cat, $camp, $depth, $code): array
{
    // Get existing and missing pages
    // ---
    $items = get_cat_exists_and_missing($cat, $depth, $code, true) ?: [];
    // ---
    $len_of_exists_pages = $items['len_of_exists'];
    $items_missing = $items['missing'];
    $items_exists = $items['exists'];
    // ---

    test_print("Items missing: " . count($items_missing));

    // Check for a secondary category
    $cat2 = TablesSql::$s_camps_cat2[$camp] ?? '';

    if (!empty($cat2) && $cat2 !== $cat) {
        $items_missing = filter_items_missing_cat2($items_missing, $cat2, $depth);
    }

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

    $summary = create_summary($code, $cat, $inprocess, $missing, $len_of_exists_pages);

    return [
        "inprocess" => $inprocess,
        "exists" => $items_exists,
        "missing" => $missing,
        "ix" => $summary,
    ];
}
