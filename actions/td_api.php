<?php

namespace Actions\TDApi;
/*
Usage:

use function Actions\TDApi\get_td_api;
use function Actions\TDApi\compare_it;

*/

use function Actions\Functions\test_print;

function compare_it($t1, $t2)
{
    echo "<br>fetch_query:<br>";
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
    $usr_agent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

    $ch = curl_init();

    $url = "{$endPoint}?" . http_build_query($params);

    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    // curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $output = curl_exec($ch);
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

function get_td_api(array $params): array
{
    $endPoint = ($_SERVER['SERVER_NAME'] == 'localhost') ? 'http://localhost:9001' : 'https://mdwiki.toolforge.org';
    $endPoint .= '/api.php';
    //---
    $out = post_url($endPoint, $params);
    //---
    $result = json_decode($out, true);
    //---
    if (!is_array($result)) {
        $result = array();
    }
    //---
    $result = $result['results'] ?? array();
    //---
    // var_dump(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    //---
    return $result;
}
