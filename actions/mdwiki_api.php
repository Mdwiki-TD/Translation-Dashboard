<?php
include_once(__DIR__ . '/../infos/user_account_new.php');

$usr_agent = $user_agent;

function get_url_params_result(string $endPoint, array $params = []): string
{
    global $usr_agent;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $output = curl_exec($ch);
    $url = "{$endPoint}?" . http_build_query($params);
    if ($output === FALSE) {
        echo ("<br>cURL Error: " . curl_error($ch) . "<br>$url");
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
