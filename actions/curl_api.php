<?php

namespace Actions\CurlApi;
/*
Usage:
require_once __DIR__ . '/../actions/curl_api.php';
use function Actions\CurlApi\post_url_params_result;
*/

use function Actions\TestPrint\test_print;

function post_url_params_result(string $endPoint, array $params = []): string
{
    $usr_agent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    // curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $output = curl_exec($ch);
    $url = "{$endPoint}?" . http_build_query($params);
    // ---
    // remove "&format=json" from $url then make it link <a href="$url2">
    $url2 = str_replace('&format=json', '', $url);
    $url2 = "<a target='_blank' href='$url2'>$url2</a>";
    //---
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //---
    if ($http_code !== 200) {
        test_print('Error: API request failed with status code ' . $http_code);
    }
    //---
    test_print("post_url_params_result:(http_code: $http_code) $url2");
    // ---
    if ($output === FALSE) {
        test_print("cURL Error: " . curl_error($ch));
    }

    if (curl_errno($ch)) {
        test_print('Error:' . curl_error($ch));
    }


    curl_close($ch);
    return $output;
}
