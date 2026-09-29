<?php

namespace App\APICalls\TDApi;

use App\Settings;
use App\Logger;


function post_url(string $ServerUrl, array $params = []): string
{
    if (empty($params)) return "";

    $timeStart = microtime(true);
    $usrAgent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

    $ch = curl_init();

    $url = "{$ServerUrl}?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => $usrAgent,
        // CURLOPT_COOKIEJAR => "cookie.txt",
        // CURLOPT_COOKIEFILE => "cookie.txt",
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 8,
    ]);

    $output = curl_exec($ch);

    // remove "&format=json" from $url then make it link <a href="$url2">
    $url2 = str_replace('&format=json', '', $url);
    $url2 = "<a target='_blank' href='$url2'>$url2</a>";

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 200) {
        Logger::debug('post_url: Error: API request failed with status code ' . $httpCode);
    }

    $executionTime = (microtime(true) - $timeStart);
    $executionTime = round($executionTime, 4);

    Logger::debug("post_url (time: $executionTime s): (http_code: $httpCode) $url2");

    if ($output === FALSE) {
        Logger::debug("post_url: cURL Error: " . curl_error($ch));
    }

    if (curl_errno($ch)) {
        Logger::debug('post_url: Error:' . curl_error($ch));
    }

    curl_close($ch);
    return $output;
}

function get_td_api(array $params): array
{
    $settings = Settings::getInstance();

    $ServerUrl = $settings->ServerUrl . '/api.php';

    $out = post_url($ServerUrl, $params);

    $apiResults = json_decode($out, true);

    if (!is_array($apiResults)) {
        $apiResults = [];
    }

    $result = $apiResults['results'] ?? [];

    if (isset($result['error'])) {
        Logger::debug('Error:' . json_encode($result['error'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    return $apiResults;
}
