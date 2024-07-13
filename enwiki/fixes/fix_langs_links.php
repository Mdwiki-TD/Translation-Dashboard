<?php
namespace EnWiki\Fixes\fix_langs_links;

/*
Usage:

use function EnWiki\Fixes\fix_langs_links\remove_lang_links;

*/


function remove_lang_links($text)
{
    // ---
    global $code_to_lang;
    // ---
    // make patern like (ar|en|de)
    $langs = implode('|', array_keys($code_to_lang));
    // ---
    preg_match_all("/\[\[($langs):[^\]]+\]\]/", $text, $matches);
    // ---
    foreach ($matches[0] as $link) {
        $text = str_replace($link, '', $text);
    }
    // ---
    // echo "<pre>";
    // echo htmlentities(var_export($matches, true));
    // echo "</pre><br>";
    // ---
    return $text;
}
