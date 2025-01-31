<?php

namespace Results\GetCats;
/*
Usage:
use function Results\GetCats\start_with;
use function Results\GetCats\get_in_process;
use function Results\GetCats\open_json_file;
use function Results\GetCats\get_cat_from_cache;
use function Results\GetCats\get_categorymembers;
use function Results\GetCats\get_mmbrs;
use function Results\GetCats\get_mdwiki_cat_members;
*/

include_once __DIR__ . '/../Tables/tables.php';
include_once __DIR__ . '/../Tables/langcode.php';
include_once __DIR__ . '/../actions/functions.php';

use function Actions\Functions\test_print;
use function Actions\MdwikiApi\get_mdwiki_url_with_params;
use function SQLorAPI\Get\get_in_process_tdapi;

function start_with($haystack, $needle)
{
    return strpos($haystack, $needle) === 0;
}

function get_in_process($missing, $code)
{
    $res = get_in_process_tdapi($code);
    $titles = array_filter($res, fn ($t) => in_array($t['title'], $missing));
    return $titles;
}

function open_json_file($file_path)
{
    if (!is_file($file_path)) {
        test_print("$file_path does not exist");
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

function get_cat_from_cache($cat)
{
    $tables_dir = getenv('tables_dir') ?? __DIR__ . '/../../td/Tables';
    if (substr($tables_dir, 0, 2) == 'I:') {
        $tables_dir = 'I:/mdwiki/mdwiki/public_html/td/Tables';
    }

    $file_path = "$tables_dir/cats_cash/$cat.json";
    $new_list = open_json_file($file_path);

    if (!isset($new_list['list']) || !is_array($new_list['list'])) {
        test_print("Invalid format in JSON file $file_path");
        return [];
    }

    return array_filter(
        $new_list['list'],
        fn ($value) =>
        !preg_match('/^(Category|File|Template|User):/', $value) &&
            !preg_match('/\(disambiguation\)$/', $value)
    );
}

function get_categorymembers($cat)
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
            if ($pages["ns"] == 0 || $pages["ns"] == 14) {
                $items[] = $pages["title"];
            }
        }
    }

    test_print("get_categorymembers() items size:" . count($items));
    return $items;
}

function get_mmbrs($cat, $use_cache = true)
{
    // $all = $use_cache || $_SERVER['SERVER_NAME'] == 'localhost' ? get_cat_from_cache($cat) : get_categorymembers($cat);
    $all = $use_cache ? get_cat_from_cache($cat) : get_categorymembers($cat);
    return empty($all) ? get_categorymembers($cat) : $all;
}

function get_mdwiki_cat_members($cat, $use_cache = true, $depth = 0, $camp = '')
{
    $titles = [];
    $cats = [$cat];
    $depth_done = -1;

    while (count($cats) > 0 && $depth > $depth_done) {
        $cats2 = [];

        foreach ($cats as $cat1) {
            $all = get_mmbrs($cat1, $use_cache);
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
    $newtitles = array_filter(
        $titles,
        fn ($title) =>
        !preg_match('/^(File|Template|User):/', $title) &&
            !preg_match('/\(disambiguation\)$/', $title)
    );

    test_print("newtitles size:" . count($newtitles));
    test_print("end of get_mdwiki_cat_members <br>===============================");

    return $newtitles;
}
