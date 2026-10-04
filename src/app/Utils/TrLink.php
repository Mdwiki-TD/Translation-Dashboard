<?php

namespace App\Utils\TrLink;

use App\SQLorAPI\SettingsTable;

function makeContentTranslationUrl(
    string $title,
    string $cod,
    string $cat,
    string $campaign,
    string $tra_type,
    string $endpoint = ""
): string {
    if (!$endpoint) {
        $endpoint = (SettingsTable::getInstance())->getEndpoint();
    };
    $title = str_replace('%20', '_', $title);

    $params = [
        'title' => 'Special:ContentTranslation',
        'tr_type' => $tra_type,
        'from' => 'mdwiki',
        'to' => $cod,
        'campaign' => $campaign,
        'page' => $title
    ];

    $url = $endpoint . "?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

    return $url;
}

function makeTrLinkMedwiki(
    string $title,
    string $cod,
    string $cat,
    string $campaign,
    string $tra_type,
    int|string $word
): string {
    $cat2   = rawurlEncode($cat);
    $camp2  = rawurlEncode($campaign);
    $title2 = rawurlEncode($title);

    $params = array(
        "title" => $title2,
        "code" => $cod,
        "cat" => $cat2,
        "camp" => $camp2,
        "word" => $word,
        "type" => $tra_type
    );

    $url = 'translate_med/index.php?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

    return $url;
}
