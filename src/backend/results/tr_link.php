<?php

namespace Results\TrLink;

/*
Usage:

use function Results\TrLink\make_translate_link_medwiki; // make_translate_link_medwiki($title, $cod, $cat, $camp, $tra_type)
use function Results\TrLink\make_tr_link_medwiki;

*/

use Tables\SqlTables\TablesSql;
use function SQLorAPI\GetDataTab\get_td_or_sql_settings;

function get_endpoint()
{
    // ---
    static $settings1 = [];
    // ---
    if (empty($settings1)) {
        $settings1 = get_td_or_sql_settings();
        $settings1 = array_column($settings1, 'value', 'title');
    }
    // ---
    $use_mdwikicx = $settings1['use_mdwikicx'] ?? '0';
    // ---
    $endpoint = "https://medwiki.toolforge.org/w/index.php";
    // ---
    if ($use_mdwikicx != '0') {
        $endpoint = "https://mdwikicx.toolforge.org/w/index.php";
    };
    // ---
    return $endpoint;
}

function make_translate_link_medwiki($title, $cod, $cat, $camp, $tra_type)
{
    // ---
    $endpoint = get_endpoint();
    // ---
    $campain = TablesSql::$s_cat_to_camp[$cat] ?? $cat;
    // ---
    // ?title=Special:ContentTranslation&from=mdwiki&to=ary&campaign=contributionsmenu&page=Dracunculiasis&targettitle=Dracunculiasis
    // ---
    $title = str_replace('%20', '_', $title);
    // ---
    $params = [
        'title' => 'Special:ContentTranslation',
        'tr_type' => $tra_type,
        'from' => 'mdwiki',
        'to' => $cod,
        'campaign' => $campain,
        'page' => $title
    ];
    // ---
    $url = $endpoint . "?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    // ---
    return $url;
}

function make_tr_link_medwiki($title, $cod, $cat, $camp, $tra_type, $word)
{
    // ---
    $cat2   = rawurlEncode($cat);
    $camp2  = rawurlEncode($camp);
    $title2 = rawurlEncode($title);
    //---
    $params = array(
        "title" => $title2,
        "code" => $cod,
        "cat" => $cat2,
        "camp" => $camp2,
        "word" => $word,
        "type" => $tra_type
    );
    //---
    $url = 'translate_med/index.php?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    //---
    return $url;
}
