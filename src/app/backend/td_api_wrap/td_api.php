<?php

namespace APICalls\TDApi;

use OAuth\Settings\Settings;

function test_print_z($s): void
{
    if (isset($_COOKIE['test']) && $_COOKIE['test'] == 'x') {
        return;
    }
    $print_t = (isset($_REQUEST['test']) || isset($_COOKIE['test'])) ? true : false;

    if ($print_t && is_string($s)) {
        echo "\n<br>\n$s";
    } elseif ($print_t) {
        echo "\n<br>\n";
        print_r($s);
    }
}

function post_url(string $endPoint, array $params = []): string
{
    if (empty($params)) return "";

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

    // remove "&format=json" from $url then make it link <a href="$url2">
    $url2 = str_replace('&format=json', '', $url);
    $url2 = "<a target='_blank' href='$url2'>$url2</a>";

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code !== 200) {
        test_print_z('post_url: Error: API request failed with status code ' . $http_code);
    }

    $execution_time = (microtime(true) - $time_start);
    $execution_time = round($execution_time, 4);

    test_print_z("post_url (time: $execution_time s): (http_code: $http_code) $url2");

    if ($output === FALSE) {
        test_print_z("post_url: cURL Error: " . curl_error($ch));
    }

    if (curl_errno($ch)) {
        test_print_z('post_url: Error:' . curl_error($ch));
    }

    curl_close($ch);
    return $output;
}

function get_td_api(array $params): array
{
    $settings = Settings::getInstance();
    $endPoint = $settings->ServerUrl;

    $endPoint .= '/api.php';

    $out = post_url($endPoint, $params);

    $api_results = json_decode($out, true);

    if (!is_array($api_results)) {
        $api_results = [];
    }

    $result = $api_results['results'] ?? [];

    if (isset($result['error'])) {
        test_print_z('Error:' . json_encode($result['error'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    return $api_results;
}
