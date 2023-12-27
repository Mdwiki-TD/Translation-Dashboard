<?php
// translate.php

// require_once __DIR__ . '/../vendor/autoload.php';
// use WikiConnect\ParseWiki\ParserCategorys;
// use WikiConnect\ParseWiki\ParserCitations;

require_once __DIR__ . '/../actions/mdwiki_api.php';
require_once 'en_api.php';
require_once 'fixtext.php';
require_once 'fixref.php';

function get_text_from_mdwiki($title, $wholearticle) {
    //---
    $params2 = array("action" => "parse", "format" => "json", "page" => $title, "prop" => "wikitext");
    //---
    $json2 = get_url_with_params($params2);
    //---
    $alltext = $json2["parse"]["wikitext"]["*"] ?? '';
    //---
    $first = '';
    //---
    if ($wholearticle) {
        $first = $alltext;
    } else {
        $params = array("action" => "parse", "format" => "json", "page" => $title, "section" => "0", "prop" => "wikitext");
        $json1 = get_url_with_params($params);
        $first = $json1["parse"]["wikitext"]["*"] ?? '';
    }
    //---
    $text = $first;
    //---
    if ($text === '') {
        return "notext";
    }
    //---
    if (!$wholearticle) {
        $text .= "\n==References==\n<references />";
    }
    //---
    return [$text, $alltext];
}

function prase_text($title, $wholearticle, $text_fix=true, $refs_fix=true) {
    //---
    $txt = get_text_from_mdwiki($title, $wholearticle);
    //---
    $text    = $txt[0];
    $alltext = $txt[1];
    //---
    // test_print("<br>prase_text: text:$text<br>");
    // test_print("<br>prase_text: alltext:$alltext<br>");
    # ---
    $newtext = $text;
    # ---
    // $parser = new ParserCitations($alltext);
    // $u = $parser->parse($parser->getCitations());
    // print_r($u);
    // print_r($parser->getCitations());
    // exit;
    //---
    // if ($refs_fix) {
    $newtext = fix_ref($newtext, $alltext);
    // }
    //---
    // if ($text_fix) {
    $newtext = text_changes_work($newtext);
    // }
    //---
    $newtext = str_replace('[[Category:', '[[:Category:', $newtext);
    //---
    if ($newtext === '') {
        echo ('no text');
        return "";
    }
    //---
    return $newtext;
}

function start_trans_php($title, $tra_type) {
    //---
    /*
    1. get text from mdwiki.org
    2. fix ref
    3. fix text
    4. put to enwiki
    5. return result
    */
    $wholearticle = $tra_type == 'all' ? true : false;
    //---
    $title2 = str_replace(' ', '_', $title);
    //---
    $newtext = prase_text($title2, $wholearticle); 
    //---
    $suus = 'from https://mdwiki.org/wiki/' . str_replace(' ', '_', $title);
    //---
    $title2 = 'User:Mr. Ibrahem/' . $title;
    //---
    if ($wholearticle) {
        $title2 = 'User:Mr. Ibrahem/' . $title . '/full';
    }
    //---
    $result = do_edit( $title2, $newtext, $suus );
    //---
    $Success = $result['edit']['result'] ?? '';
    //---
    if ($Success == 'Success') {
        return 'true';
    }
    //---
    return $Success;
}
//---