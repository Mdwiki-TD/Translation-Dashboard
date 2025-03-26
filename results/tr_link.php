<?php

namespace Results\TrLink;

/*
Usage:

use function Results\TrLink\make_translate_link;
use function Results\TrLink\make_translate_link_medwiki;
use function Results\TrLink\make_tr_link_medwiki;

*/
function make_translate_link_medwiki($title, $cod, $cat, $camp, $tra_type)
{
    // ---
    global $cat_to_camp;
    // ---
    $campain = $cat_to_camp[$cat] ?? $cat;
    // ---
    // $endpoint = "https://medwiki.toolforge.org/w/index.php";
    $endpoint = "https://mdwikicx.toolforge.org/w/index.php";
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

function make_translate_link($title, $cod, $cat, $camp, $tra_type)
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
        "type" => $tra_type
    );
    //---
    $url = 'translate.php?' . http_build_query($params);
    //---
    return $url;
}

function make_tr_link_medwiki($title, $cod, $cat, $camp, $tra_type)
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
        "type" => $tra_type
    );
    //---
    $url = 'translate_med/medwiki.php?' . http_build_query($params);
    //---
    return $url;
}
