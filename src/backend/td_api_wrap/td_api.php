<?php

namespace APICalls\TDApi;
/*
Usage:

use function APICalls\TDApi\get_td_api;
use function APICalls\TDApi\compare_it;

*/

function test_print_o($s)
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

function compare_it($t1, $t2)
{
    echo "<br>fetch _query:<br>";
    // //---
    var_dump(json_encode($t1, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    // //---
    echo "<br>get_td_api:<br>";
    // //---
    var_dump(json_encode($t2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    //---
}

function post_url(string $endPoint, array $params = []): string
{
    $time_start = microtime(true);
    $usr_agent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

    $ch = curl_init();

    $url = "{$endPoint}?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => $usr_agent,
        // CURLOPT_COOKIEJAR => "cookie.txt",
        // CURLOPT_COOKIEFILE => "cookie.txt",
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 8,
    ]);

    $output = curl_exec($ch);
    // ---
    // remove "&format=json" from $url then make it link <a href="$url2">
    $url2 = str_replace('&format=json', '', $url);
    $url2 = "<a target='_blank' href='$url2'>$url2</a>";
    //---
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //---
    if ($http_code !== 200) {
        test_print_o('post_url: Error: API request failed with status code ' . $http_code);
    }
    //---
    $execution_time = (microtime(true) - $time_start);
    $execution_time = round($execution_time, 4);
    // ---
    test_print_o("post_url (time: $execution_time s): (http_code: $http_code) $url2");
    // ---
    if ($output === FALSE) {
        test_print_o("post_url: cURL Error: " . curl_error($ch));
    }

    if (curl_errno($ch)) {
        test_print_o('post_url: Error:' . curl_error($ch));
    }
    // ---
    curl_close($ch);
    return $output;
}

function get_td_api(array $params): array
{
    $endPoint = (getenv('APP_ENV') === 'production') ? 'https://mdwiki.toolforge.org' : 'http://localhost:9001';
    //---
    $endPoint .= '/api.php';
    //---
    $out = post_url($endPoint, $params);
    //---
    $results = json_decode($out, true);
    //---
    if (!is_array($results)) {
        $results = [];
    }
    //---
    $result = $results['results'] ?? [];
    //---
    if (isset($result['error'])) {
        test_print_o('Error:' . json_encode($result['error'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $result = [];
    }
    //---
    // var_dump(json_encode(, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    //---
    return $result;
}
