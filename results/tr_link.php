<?php

namespace Results\TrLink;

/*
Usage:

use function Results\TrLink\make_translate_link_medwiki;
use function Results\TrLink\make_tr_link_medwiki;

*/

include_once __DIR__ . '/../actions/td_api.php';
include_once __DIR__ . '/../api_or_sql/index.php';

use Tables\SqlTables\TablesSql;
use function SQLorAPI\GetDataTab\get_td_or_sql_settings;

$settings1 = get_td_or_sql_settings();
$settings1 = array_column($settings1, 'value', 'title');
// var_export($settings1);
$use_mdwikicx = $settings1['use_mdwikicx'] ?? '0';

function make_translate_link_medwiki($title, $cod, $cat, $camp, $tra_type)
{
    // ---
    global $use_mdwikicx;
    // ---
    $campain = TablesSql::$s_cat_to_camp[$cat] ?? $cat;
    // ---
    $endpoint = "https://medwiki.toolforge.org/w/index.php";
    // ---
    if ($use_mdwikicx != '0') {
        $endpoint = "https://mdwikicx.toolforge.org/w/index.php";
    };
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
    $url = $endpoint . "?" . http_build_query($params);
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
    $url = 'translate_med/index.php?' . http_build_query($params);
    //---
    return $url;
}
