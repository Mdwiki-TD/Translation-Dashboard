<?php

namespace Results\GetCats;
/*
Usage:
use function Results\GetCats\get_category_from_cache;
use function Results\GetCats\get_mdwiki_cat_members;
*/

include_once __DIR__ . '/include.php';

use function Actions\TestPrint\test_print;
use function Results\ResultsHelps\open_json_file;
use function Results\CatsAPI\fetch_category_members;

function start_with($haystack, $needle)
{
    return strpos($haystack, $needle) === 0;
}

function titles_filter($titles, $with_Category = false)
{
    $regline = ($with_Category) ? '/^(Category|File|Template|User):/' : '/^(File|Template|User):/';
    return array_filter($titles, function ($title) use ($regline) {
        return !preg_match($regline, $title) &&
            !preg_match('/\(disambiguation\)$/', $title);
    });
}

function get_category_from_cache($cat)
{
    // ---
    if (isset($_REQUEST['nocache'])) {
        return [];
    }
    // ---
    $tables_dir = getenv('tables_dir') ?? __DIR__ . '/../../td/Tables';
    if (substr($tables_dir, 0, 2) == 'I:') {
        $tables_dir = 'I:/mdwiki/mdwiki/public_html/td/Tables';
    }

    $file_path = "$tables_dir/cats_cash/$cat.json";
    $new_list = open_json_file($file_path) ?? [];

    if (empty($new_list)) {
        // test_print("File: $file_path empty or not exists");
        return [];
    }

    if (!isset($new_list['list']) || !is_array($new_list['list'])) {
        test_print("Invalid format in JSON file $file_path");
        return [];
    }
    test_print("File: cats_cash/$cat.json: Exists size: " . count($new_list['list']));

    return titles_filter($new_list['list'], true);
}

function get_category_members($cat, $use_cache = true)
{
    // $all = $use_cache || $_SERVER['SERVER_NAME'] == 'localhost' ? get_category_from_cache($cat) : fetch_category_members($cat);
    // ---
    // $all = $use_cache ? get_category_from_cache($cat) : fetch_category_members($cat);
    // return empty($all) ? fetch_category_members($cat) : $all;
    // ---
    $all = [];
    // ---
    if ($use_cache) {
        $all = get_category_from_cache($cat);
    }
    // ---
    if (empty($all)) {
        $all = fetch_category_members($cat);
    }
    // ---
    test_print("get_category_members all size: " . count($all));
    // ---
    return $all;
}

// function get_mdwiki_cat_members($cat, $use_cache = true, $depth = 0, $camp = '')
function get_mdwiki_cat_members($cat, $depth, $use_cache)
{
    $titles = [];
    $cats = [];
    $cats[] = $cat;
    $depth_done = -1;

    while (count($cats) > 0 && $depth > $depth_done) {
        $cats2 = [];

        foreach ($cats as $cat1) {
            $all = get_category_members($cat1, $use_cache);
            foreach ($all as $title) {
                if (start_with($title, 'Category:')) {
                    $cats2[] = $title;
                } else {
                    $titles[] = $title;
                }
            }
        }

        $depth_done++;
        $cats = $cats2;
    }

    $titles = array_unique($titles);

    $newtitles = titles_filter($titles);
    test_print("get_mdwiki_cat_members newtitles size:" . count($newtitles));
    // test_print("end of get_mdwiki_cat_members <br>===============================");

    return $newtitles;
}
