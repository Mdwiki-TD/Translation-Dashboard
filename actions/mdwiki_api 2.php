<?php

namespace Actions\MdwikiApi;
/*
Usage:
use function Actions\MdwikiApi\get_url_params_result;
use function Actions\MdwikiApi\get_mdwiki_url_with_params;
*/

include_once(__DIR__ . '/../infos/user_account_new.php');

use function Actions\Functions\test_print;

$usr_agent = $user_agent;

// Constants for rate limiting
define('RATE_LIMIT', 100); // Maximum number of requests
define('RATE_LIMIT_TIME', 3600); // Time window in seconds (e.g., 1 hour)

function get_url_params_result(string $endPoint, array $params = []): string
{
    global $usr_agent;

    // Check rate limit
    if (!check_rate_limit($_SERVER['REMOTE_ADDR'])) {
        return json_encode(['error' => 'Rate limit exceeded']);
    }

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

function check_rate_limit($ip)
{
    $filename = 'rate_limit.txt';
    $rateLimitData = [];

    if (file_exists($filename)) {
        $rateLimitData = json_decode(file_get_contents($filename), true);
    }

    $currentTime = time();
    if (!isset($rateLimitData[$ip])) {
        $rateLimitData[$ip] = ['count' => 0, 'time' => $currentTime];
    }

    // Reset count if time window has passed
    if ($currentTime - $rateLimitData[$ip]['time'] > RATE_LIMIT_TIME) {
        $rateLimitData[$ip]['count'] = 0;
        $rateLimitData[$ip]['time'] = $currentTime;
    }

    $rateLimitData[$ip]['count']++;

    // Save rate limit data
    file_put_contents($filename, json_encode($rateLimitData));

    // Check if rate limit is exceeded
    if ($rateLimitData[$ip]['count'] > RATE_LIMIT) {
        return false;
    }

    return true;
}
