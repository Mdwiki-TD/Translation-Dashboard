<?php

namespace Results\GetCats;
/*
Usage:
use function Results\GetCats\get_cats_from_cache;
use function Results\GetCats\get_mdwiki_cat_members;
*/

function tests_print($s)
{
    if (isset($_COOKIE['test']) && $_COOKIE['test'] == 'x') {
        return;
    }
    $print_t = (isset($_REQUEST['test']) || isset($_COOKIE['test'])) ? true : false;
    if ($print_t && gettype($s) == 'string') {
        echo "\n<br>\n$s";
    } elseif ($print_t) {
        echo "\n<br>\n";
        print_r($s);
    }
}
function starts_with($haystack, $needle)
{
    return strpos($haystack, $needle) === 0;
}

function post_urls_mdwiki(string $endPoint, array $params = []): string
{
    $usr_agent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

    $ch = curl_init();
    // ---
    curl_setopt_array($ch, [
        CURLOPT_URL => $endPoint,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($params, '', '&', PHP_QUERY_RFC3986),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => $usr_agent,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 5,
        // لاستخدام ملفات الكوكيز عند الحاجة:
        // CURLOPT_COOKIEJAR => "cookie.txt",
        // CURLOPT_COOKIEFILE => "cookie.txt",
    ]);
    // ---
    $output = curl_exec($ch);
    // ---
    $url = "{$endPoint}?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    // ---
    // remove "&format=json" from $url then make it link <a href="$url2">
    $url2 = str_replace('&format=json', '', $url);
    $url2 = "<a target='_blank' href='$url2'>$url2</a>";
    //---
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //---
    if ($http_code !== 200) {
        tests_print('post_urls_mdwiki: Error: API request failed with status code ' . $http_code);
    }
    //---
    tests_print("post_urls_mdwiki: (http_code: $http_code) $url2");
    // ---
    if ($output === FALSE) {
        tests_print("post_urls_mdwiki: cURL Error: " . curl_error($ch));
    }

    if (curl_errno($ch)) {
        tests_print('post_urls_mdwiki: Error:' . curl_error($ch));
    }


    curl_close($ch);
    return $output;
}

function get_mdwiki_urls_with_params(array $params): array
{
    $endPoint = 'https://mdwiki.org/w/api.php';
    //---
    $out = post_urls_mdwiki($endPoint, $params);
    //---
    $result = json_decode($out, true);
    //---
    if (!is_array($result)) {
        $result = [];
    }
    //---
    return $result;
}

function fetch_cats_members_api($cat)
{
    if (!starts_with($cat, 'Category:')) {
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

        $resa = get_mdwiki_urls_with_params($params);
        if (!isset($resa["query"]) || !isset($resa["query"]["categorymembers"])) {
            tests_print("Error fetching category members for '$cat'");
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

    tests_print("fetch_cats_members_api() items size:" . count($items));

    return $items;
}

function fetch_cats_members($cat)
{
    $cache_key = "Category_members_" . md5($cat);
    $cache_ttl = 3600 * 12;

    $items = false;
    if (function_exists('apcu_fetch')) {
        $items = apcu_fetch($cache_key);

        if (empty($items) || ($cat === "RTT" && is_array($items) && count($items) < 3000)) {
            apcu_delete($cache_key);
            $items = false;
        }
    }
    if ($items === false) {
        $items = fetch_cats_members_api($cat);
        tests_print("apcu_store() size:" . count($items) . " cat: $cat");
        if (function_exists('apcu_store')) {
            apcu_store($cache_key, $items, $cache_ttl);
        }
    } else {
        tests_print("apcu_fetch() size:" . count($items) . " cat: $cat");
    }

    return $items;
}

function open_tables_file($path, $echo = true)
{
    $tables_dir = getenv("HOME") . '/public_html/td/Tables';
    //---
    if (substr(__DIR__, 0, 2) == 'I:') {
        $tables_dir = 'I:/mdwiki/mdwiki/public_html/td/Tables';
    }
    //---
    $file_path = "$tables_dir/$path";
    //---
    if (!is_file($file_path)) {
        tests_print("---- open_tables_file: file $file_path does not exist");
        return [];
    }
    $contents = file_get_contents($file_path);

    if ($contents === false) {
        tests_print("---- Failed to read file contents from $file_path");
        return [];
    }

    $result = json_decode($contents, true);

    if ($result === null || $result === false) {
        tests_print("---- Failed to decode JSON from $file_path");
        $result = [];
    } elseif ($echo) {
        $len = count($result);
        if (isset($result['list'])) $len = count($result['list']);
        // ---
        tests_print("---- open_tables_file File: $file_path: Exists size: $len");
    }

    return $result;
}


function titles_filters($titles, $with_Category = false)
{
    $regline = ($with_Category) ? '/^(Category|File|Template|User):/' : '/^(File|Template|User):/';
    return array_filter($titles, function ($title) use ($regline) {
        return !preg_match($regline, $title) &&
            !preg_match('/\(disambiguation\)$/', $title);
    });
}

function get_cats_from_cache($cat)
{
    // ---
    if (isset($_GET['nocache'])) {
        return [];
    }
    // ---
    $file_path = "cats_cash/$cat.json";
    $new_list = open_tables_file($file_path, false) ?? [];

    if (empty($new_list)) {
        // tests_print("File: $file_path empty or not exists");
        return [];
    }

    if (!isset($new_list['list']) || !is_array($new_list['list'])) {
        tests_print("Invalid format in JSON file $file_path");
        return [];
    }
    // tests_print("File: cats_cash/$cat.json: Exists size: " . count($new_list['list']));

    return titles_filters($new_list['list'], true);
}

function get_cats_members($cat, $use_cache = true)
{
    // $all = $use_cache || $_SERVER['SERVER_NAME'] == 'localhost' ? get_cats_from_cache($cat) : fetch_cats_members($cat);
    // ---
    // $all = $use_cache ? get_cats_from_cache($cat) : fetch_cats_members($cat);
    // return empty($all) ? fetch_cats_members($cat) : $all;
    // ---
    $all = [];
    // ---
    if ($use_cache) {
        $all = get_cats_from_cache($cat);
    }
    // ---
    if (empty($all)) {
        $all = fetch_cats_members($cat);
    }
    // ---
    tests_print("get_cats_members all size: " . count($all));
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
            $all = get_cats_members($cat1, $use_cache);
            foreach ($all as $title) {
                if (starts_with($title, 'Category:')) {
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

    $newtitles = titles_filters($titles);
    tests_print("get_mdwiki_cat_members newtitles size:" . count($newtitles));
    // tests_print("end of get_mdwiki_cat_members <br>===============================");

    return $newtitles;
}
