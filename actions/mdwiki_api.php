<?php

function get_url_params_result(string $endPoint, array $params = []): string
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $output = curl_exec($ch);

    if ($output === FALSE) {
        test_print("cURL Error: " . curl_error($ch));
    }

    curl_close($ch);
    return $output;
}


function get_mdwiki_url_with_params(array $params): array
{
    $endPoint = 'https://mdwiki.org/w/api.php';
    $query = http_build_query($params);
    $url = "{$endPoint}?{$query}";
    //---
    // remove "&format=json" from $url then make it link <a href="$url2">
    $url2 = str_replace('&format=json', '', $url);
    $url2 = "<a target='_blank' href='$url2'>$url2</a>";
    //---
    test_print("<br>get_mdwiki_url_with_params: $url2<br>");
    //---
    $out = get_url_params_result($endPoint, $params);
    //---
    $result = json_decode($out, true);
    //---
    if (!is_array($result)) {
        $result = array();
    }
    //---
    return $result;
}

function get_api_php(array $params): array
{
    $endPoint = '/Translation_Dashboard/auth/api.php';
    //---
    $query = http_build_query($params);
    //---
    $url = "{$endPoint}?{$query}";
    //---
    test_print("<br>get_api_php: $url<br>");
    //---
    $out = get_url_params_result($endPoint, $params);
    //---
    $result = json_decode($out, true);
    //---
    if (!is_array($result)) {
        $result = array();
    }
    //---
    return $result;
}
