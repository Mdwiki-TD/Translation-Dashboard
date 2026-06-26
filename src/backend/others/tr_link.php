<?php

namespace Results\TrLink;

/*
Usage:

use function Results\TrLink\make_ContentTranslation_url; // make_ContentTranslation_url($title, $cod, $cat, $camp, $tra_type)
use function Results\TrLink\make_tr_link_medwiki;

*/

function make_ContentTranslation_url(
    $title,
    $cod,
    $cat,
    $campaign,
    $tra_type,
    $endpoint
) {
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
        'campaign' => $campaign,
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
