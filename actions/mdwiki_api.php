<?php
//---
function get_url_with_params(array $params): array {
    $endPoint = 'https://mdwiki.org/w/api.php';
    $query = http_build_query($params);
    $url = "{$endPoint}?{$query}";
    test_print("<br>get_url_with_params: $url<br>");
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