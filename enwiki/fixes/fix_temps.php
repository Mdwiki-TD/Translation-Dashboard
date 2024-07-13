<?php
namespace EnWiki\Fixes\FixTemps;

/*
Usage:

use function EnWiki\Fixes\FixTemps\remove_templates;

*/

// include_once __DIR__ . '/../vendor_load.php';
use function WikiParse\Template\getTemplate;

$tempsToDelete = [
    "short description",
    "toc limit",
    'use american english',
    'use mdy dates',
    'use dmy dates',
    'sprotect',
    'about',
    'featured article',
    'redirect',
    '#unlinkedwikibase'
];

function remove_templates($text)
{
    global $tempsToDelete;
    // ---
    $pattern = "/\{{2}((?>[^\{\}]+)|(?R))*\}{2}/x";
    preg_match_all($pattern, $text, $matches);
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
        // ---
        $temp = getTemplate($text_template);
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
    return trim($text);
}
