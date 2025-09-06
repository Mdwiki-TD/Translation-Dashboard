<?php

namespace Results\CatsAPI;
/*
Usage:
use function Results\CatsAPI\fetch_category_members;
*/

use function TD\Render\TestPrint\test_print;
use function Actions\MdwikiApi\get_mdwiki_url_with_params;

function start_with($haystack, $needle)
{
    return strpos($haystack, $needle) === 0;
}

function fetch_category_members_api($cat)
{
    if (!start_with($cat, 'Category:')) {
        $cat = "Category:$cat";
    }

    $params = [
        "action" => "query",
        "list" => "categorymembers",
        "cmtitle" => $cat,
        "cmlimit" => "max",
        "cmtype" => "page|subcat",
        "format" => "json"
    ];

    $items = [];
    $cmcontinue = 'x';

    while (!empty($cmcontinue)) {
        if ($cmcontinue != 'x') $params['cmcontinue'] = $cmcontinue;

        $resa = get_mdwiki_url_with_params($params);
        if (!isset($resa["query"]) || !isset($resa["query"]["categorymembers"])) {
            test_print("Error fetching category members for '$cat'");
            return $items; // Return whatever we've collected so far
        }
        $cmcontinue = $resa["continue"]["cmcontinue"] ?? '';

        $categorymembers = $resa["query"]["categorymembers"] ?? [];
        foreach ($categorymembers as $pages) {
            if ($pages["ns"] == 0 || $pages["ns"] == 14 || $pages["ns"] == 3000) {
                $items[] = $pages["title"];
            }
        }
    }

    test_print("fetch_category_members_api() items size:" . count($items));

    return $items;
}

function fetch_category_members_OLD($cat)
{
    // ---
    $items = fetch_category_members_api($cat);
    // ---
    return $items;
}

function fetch_category_members($cat)
{
    $cache_key = "Category_members_" . md5($cat);
    $cache_ttl = 3600 * 12;

    $items = apcu_fetch($cache_key);

    if (empty($items) || ($cat === "RTT" && is_array($items) && count($items) < 3000)) {
        apcu_delete($cache_key);
        $items = false;
    }
    if ($items === false) {
        $items = fetch_category_members_api($cat);
        test_print("apcu_store() size:" . count($items) . " cat: $cat");
        apcu_store($cache_key, $items, $cache_ttl);
    } else {
        test_print("apcu_fetch() size:" . count($items) . " cat: $cat");
    }

    return $items;
}
