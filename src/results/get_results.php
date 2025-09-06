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
    $items = get_cat_exists_and_missing($cat, $depth, $code, true) ?: [];
    $len_of_exists_pages = $items['len_of_exists'];
    $items_missing = $items['missing'];

    $items_exists = $items['exists'];

    test_print("Items missing: " . count($items_missing));

    // Check for a secondary category
    $cat2 = TablesSql::$s_camps_cat2[$camp] ?? '';
    if (!empty($cat2) && $cat2 !== $cat) {
        // $cat2_members = get_mdwiki_cat_members($cat2, $use_cache = true, $depth = $depth, $camp = $camp);
        $cat2_members = get_mdwiki_cat_members($cat2, $depth, true);
        $items_missing = array_intersect($items_missing, $cat2_members);
        test_print("Items missing after intersecting with cat2: " . count($items_missing));
    }

    test_print("Length of existing pages: $len_of_exists_pages");

    // Remove duplicates from missing items
    $missing = array_unique($items_missing);

    // Get in-process items
    $inprocess = getinprocess($missing, $code);
    $len_inprocess = count($inprocess);

    // Calculate totals
    $len_of_missing_pages = count($missing);
    $len_of_all = $len_of_exists_pages + $len_of_missing_pages;

    // Prepare category URL
    $cat2 = "Category:" . str_replace('Category:', '', $cat);
    $caturl = "<a href='https://mdwiki.org/wiki/$cat2'>category</a>";

    // Generate summary message
    $ix = "Found $len_of_all pages in $caturl, $len_of_exists_pages exists, and $len_of_missing_pages missing in (<a href='https://$code.wikipedia.org'>$code</a>), $len_inprocess In process.";

    // Remove in-process items from missing list
    if ($len_inprocess > 0) {
        $inprocess_2 = array_column($inprocess, 'title');
        $missing = array_diff($missing, $inprocess_2);
    }

    return [
        "inprocess" => $inprocess,
        "exists" => $items_exists,
        "missing" => $missing,
        "ix" => $ix,
    ];
}
