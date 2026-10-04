<?php

namespace App\Utils;

use App\SQLorAPI\SettingsTable;

class TrLink
{
    public static function makeContentTranslationUrl(
        string $title,
        string $cod,
        string $cat,
        string $campaign,
        string $tra_type,
        string $endpoint = ""
    ): string {
        if (!$endpoint) {
            $endpoint = (SettingsTable::getInstance())->getEndpoint();
        }
        $title = str_replace('%20', '_', $title);

        $params = [
            'title' => 'Special:ContentTranslation',
            'tr_type' => $tra_type,
            'from' => 'mdwiki',
            'to' => $cod,
            'campaign' => $campaign,
            'page' => $title
        ];

        return $endpoint . "?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    public static function makeTrLinkMedwiki(
        string $title,
        string $cod,
        string $cat,
        string $campaign,
        string $tra_type,
        int|string $word
    ): string {
        $cat2   = rawurlencode($cat);
        $camp2  = rawurlencode($campaign);
        $title2 = rawurlencode($title);

        $params = [
            "title" => $title2,
            "code" => $cod,
            "cat" => $cat2,
            "camp" => $camp2,
            "word" => $word,
            "type" => $tra_type
        ];

        return 'translate_med/index.php?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}
