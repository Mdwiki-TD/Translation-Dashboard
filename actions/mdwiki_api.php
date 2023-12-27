<?php
//---
function get_url_with_params(array $params): array {
    $endPoint = 'https://mdwiki.org/w/api.php';
    $query = http_build_query($params);
    $url = "{$endPoint}?{$query}";
    //---
    // remove "&format=json" from $url then make it link <a href="$url2">
    $url2 = str_replace('&format=json', '', $url);
    $url2 = "<a href='$url2'>$url2</a>";
    //---
    test_print("<br>get_url_with_params: $url2<br>");
    $result = json_decode(file_get_contents($url), true);
    return $result;
}
//---
function get_api_php(array $params): array {
    $endPoint = '/Translation_Dashboard/auth/api.php';
    $query = http_build_query($params);
    $url = "{$endPoint}?{$query}";
    test_print("<br>get_url_with_params: $url<br>");
    $result = json_decode(file_get_contents($url), true);
    return $result;
}
//---
?>