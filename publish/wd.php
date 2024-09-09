<?php

namespace Publish\WD;
/*
use function Publish\WD\LinkToWikidata;
use function Publish\WD\GetQidForMdtitle;
*/

include_once __DIR__ . '/../auth/config.php';
include_once __DIR__ . '/../auth/helps.php';
include_once __DIR__ . '/../actions/wiki_api.php';
include_once __DIR__ . '/../actions/functions.php';
include_once __DIR__ . '/../actions/mdwiki_sql.php';
include_once __DIR__ . '/helps.php';
include_once __DIR__ . '/get_token.php';

use function Publish\Helps\get_access_from_db;
use function Publish\GetToken\post_params;

use function Actions\Functions\test_print;
use function Actions\MdwikiSql\fetch_query;
use function Actions\WikiApi\get_url_result_curl;

function GetQidForMdtitle($title)
{
    $query = <<<SQL
        SELECT qid FROM qids WHERE title = ?
    SQL;
    // ---
    $params = [$title];
    // ---
    $result = fetch_query($query, $params);
    // ---
    return $result;
}

function GetTitleInfo($targettitle, $lang)
{
    // replace '/' with '%2F'
    // $targettitle = urlencode($targettitle);
    $targettitle = str_replace('/', '%2F', $targettitle);
    $targettitle = str_replace(' ', '_', $targettitle);


    $url = "https://$lang.wikipedia.org/api/rest_v1/page/summary/$targettitle";
    // ---
    test_print("GetTitleInfo url: $url");
    // ---
    $result = get_url_result_curl($url);
    // ---
    $result = json_decode($result, true);
    // ---
    return $result;
}

function LinkIt($qid, $lang, $sourcetitle, $targettitle, $access_key, $access_secret)
{
    $https_domain = "https://www.wikidata.org";
    // ---
    $apiParams = [
        "action" => "wbsetsitelink",
        "linktitle" => $targettitle,
        "linksite" => "{$lang}wiki",
    ];
    if (!empty($qid)) {
        $apiParams["id"] = $qid;
    } else {
        $apiParams["title"] = $sourcetitle;
        $apiParams["site"] = "enwiki";
    }
    // ---
    $response = post_params($apiParams, $https_domain, $access_key, $access_secret);
    // ---
    $Result = json_decode($response, true);
    // ---
    // if (isset($Result->error)) {
    if (isset($Result['error'])) {
        test_print("post_params: Result->error: " . json_encode($Result['error']));
    }
    // ---
    if ($Result == null) {
        test_print("post_params: Error: " . json_last_error() . " " . json_last_error_msg());
        test_print("response:");
        test_print($response);
    }
    // ---
    return $Result;
}

function LinkToWikidata($sourcetitle, $lang, $user, $targettitle, $access_key, $access_secret)
{
    // ---
    if (!$access_key || !$access_secret) {
        $access = get_access_from_db($user);
        if ($access == null) {
            test_print("user = $user");
            test_print("access == null");
            // ---
            return ['error' => 'access not found. for user: ' . $user];
        }
        $access_key = $access['access_key'];
        $access_secret = $access['access_secret'];
    }
    // ---
    $qids = GetQidForMdtitle($sourcetitle);
    $qid = $qids[0]['qid'] ?? '';
    // ---
    $title_info = GetTitleInfo($targettitle, $lang);
    // ---
    $ns = $title_info['namespace']["id"] ?? "";
    // ---
    // "title":"Not found."
    $Not_found = $title_info['title'] ?? "";
    test_print("ns: ($ns), Not_found: ($Not_found)");
    test_print(json_encode($title_info));
    // ---
    if ($ns != 0) {
        return ['error' => 'no link for ns:' . $ns];
    }
    // ---
    if ($Not_found == "Not found.") {
        return ['error' => 'page not found:' . $sourcetitle];
    }
    // ---
    $link_iit = LinkIt($qid, $lang, $sourcetitle, $targettitle, $access_key, $access_secret);
    // ---
    $success = $Result['success'] ?? false;
    // ---
    if ($success) {
        test_print("success: $success");
        return ['result' => "success"];
    };
    // ---
    return $link_iit;
}
