<?php
// fixtext.php

if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

require_once __DIR__ . '/../vendor/autoload.php';

use WikiConnect\ParseWiki\ParserTemplate;

// ---
$tempsToDelete = [
    "short description",
    "toc limit",
    'use american english',
    'use dmy dates',
    'sprotect',
    'about',
    'featured article',
    'redirect',
    '#unlinkedwikibase'
];

function remove_lang_links($text) {
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
function text_changes_work($text) {
    global $tempsToDelete;
    // ---
    preg_match_all("/\{{2}((?>[^\{\}]+)|(?R))*\}{2}/x", $text, $matches);
    // ---
    // echo "<pre>";
    // echo htmlentities(var_export($matches, true));
    // echo "</pre><br>";
    // ---
    foreach ($matches[0] as $text_template) {
        // if not $text contains $text_template, echo "err"
        // if (strpos($text, $text_template) === false) {
        //     echo "err";
        // }
        $parser = new ParserTemplate($text_template);
        $temp = $parser->getTemplate();
        // ---
        // echo "<pre>";
        // echo htmlentities(var_export($temp, true));
        // echo "</pre><br>";
        // ---
        $name = $temp->getName();
        // ---
        $name = trim(strtolower(str_replace('_', ' ', $name)));
        // ---
        if (in_array($name, $tempsToDelete)) {
            // echo "delete template: " . $name . "<br>";
            $text = str_replace($text_template, '', $text);
        }
        // ---
        // if $name start with "#unlinkedwikibase" delete it
        if (strpos($name, "#unlinkedwikibase") === 0) {
            // echo "delete template: " . $name . "<br>";
            $text = str_replace($text_template, '', $text);
        }
    }
    // ---
    $text = remove_lang_links($text);
    // ---
    return trim($text);
}

?>

