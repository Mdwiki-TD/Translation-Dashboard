<?php

namespace Results\GetCats;
/*
Usage:
use function Results\GetCats\start_with;
use function Results\GetCats\get_inprocess;
use function Results\GetCats\open_json_file;
use function Results\GetCats\get_category_from_cache;
use function Results\GetCats\fetch_category_members;
use function Results\GetCats\get_category_members;
use function Results\GetCats\get_mdwiki_cat_members;
*/

include_once __DIR__ . '/../actions/test_print.php';
include_once __DIR__ . '/../Tables/tables.php';
include_once __DIR__ . '/../Tables/langcode.php';
include_once __DIR__ . '/../actions/functions.php';

use function Actions\TestPrint\test_print;
use function Actions\MdwikiApi\get_mdwiki_url_with_params;
use function SQLorAPI\Get\get_inprocess_tdapi;

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

function get_inprocess($missing, $code)
{
    $res = get_inprocess_tdapi($code);
    $titles = [];
    foreach ($res as $t) {
        if (in_array($t['title'], $missing)) $titles[$t['title']] = $t;
    }
    return $titles;
}

function open_json_file($file_path)
{
    if (!is_file($file_path)) {
        test_print("file $file_path does not exist");
        return [];
    }

    $text = file_get_contents($file_path);
    if ($text === false) {
        test_print("Failed to read file contents from $file_path");
        return [];
    }

    $data = json_decode($text, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        test_print("Failed to decode JSON from $file_path");
        return [];
    }

    return $data;
}

function get_category_from_cache($cat)
{
    $tables_dir = getenv('tables_dir') ?? __DIR__ . '/../../td/Tables';
    if (substr($tables_dir, 0, 2) == 'I:') {
        $tables_dir = 'I:/mdwiki/mdwiki/public_html/td/Tables';
    }

    $file_path = "$tables_dir/cats_cash/$cat.json";
    $new_list = open_json_file($file_path) ?? [];

    if (empty($new_list)) {
        test_print("File: $file_path empty or not exists");
        return [];
    }

    if (!isset($new_list['list']) || !is_array($new_list['list'])) {
        test_print("Invalid format in JSON file $file_path");
        return [];
    }
    test_print("$file_path: Exists size: " . count($new_list['list']));

    return titles_filter($new_list['list'], $with_Category = true);
}

function fetch_category_members($cat)
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
        $cmcontinue = $resa["continue"]["cmcontinue"] ?? '';

        $categorymembers = $resa["query"]["categorymembers"] ?? [];
        foreach ($categorymembers as $pages) {
            if ($pages["ns"] == 0 || $pages["ns"] == 14 || $pages["ns"] == 3000) {
                $items[] = $pages["title"];
            }
        }
    }

    test_print("fetch_category_members() items size:" . count($items));
    return $items;
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
