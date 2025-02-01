<?php

namespace Results\GetResults;

/*
Usage:

use function Results\GetResults\get_results;

*/

// use function Results\FetchCatData\get_cat_exists_and_missing;
use function Results\FetchCatDataSparql\get_cat_exists_and_missing;
use function Results\GetCats\get_in_process;
use function Results\GetCats\get_mdwiki_cat_members;
use function Actions\Functions\test_print;

/**
 * Get results for a category, including missing pages and in-process items.
 *
 * @param string $cat   Category name.
 * @param string $camp  Campaign name.
 * @param int    $depth Depth of category traversal.
 * @param string $code  Language code.
 * @return array        Array containing in-process items, missing pages, and summary info.
 */

function get_results($cat, $camp, $depth, $code): array
{
    // Get existing and missing pages
    $items = get_cat_exists_and_missing($cat, $depth, $code, true) ?: [];
    $len_of_exists_pages = $items['len_of_exists'];
    $items_missing = $items['missing'];

    test_print("Items missing: " . count($items_missing));

    // Check for a secondary category
    $cat2 = $camps_cat2[$camp] ?? '';
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
    $in_process = get_in_process($missing, $code);
    $len_in_process = count($in_process);

    // Calculate totals
    $len_of_missing_pages = count($missing);
    $len_of_all = $len_of_exists_pages + $len_of_missing_pages;

    // Prepare category URL
    $cat2 = "Category:" . str_replace('Category:', '', $cat);
    $caturl = "<a href='https://mdwiki.org/wiki/$cat2'>category</a>";

    // Generate summary message
    $ix = "Found $len_of_all pages in $caturl, $len_of_exists_pages exists, and $len_of_missing_pages missing in (<a href='https://$code.wikipedia.org'>$code</a>), $len_in_process In process.";

    // Remove in-process items from missing list
    if ($len_in_process > 0) {
        $missing = array_diff($missing, array_keys($in_process));
    }

    return [
        "in_process" => $in_process,
        "missing" => $missing,
        "ix" => $ix,
    ];
}
