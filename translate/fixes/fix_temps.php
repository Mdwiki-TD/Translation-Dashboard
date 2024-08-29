<?php

namespace Translate\Fixes\FixTemps;

/*
Usage:

use function Translate\Fixes\FixTemps\remove_templates;
use function Translate\Fixes\FixTemps\remove_templates_lead;
use function Translate\Fixes\FixTemps\add_missing_title;

*/

// include_once __DIR__ . '/../vendor_load.php';
use function WikiParse\Template\getTemplate;

$tempsToDelete = [
    "#unlinkedwikibase",
    "about",
    "anchor",
    "defaultsort",
    "distinguish",
    "esborrany",
    "featured article",
    "fr",
    "good article",
    "italic title",
    "other uses",
    "redirect",
    "redirect-distinguish",
    "see also",
    "short description",
    "sprotect",
    "tedirect-distinguish",
    "toc limit",
    "use american english",
    "use dmy dates",
    "use mdy dates",
    "void",
];

$temps_patterns = [
    // any template startswith pp-
    "/^pp(-.*)?$/",
    "/^articles (for|with|needing|containing).*$/",
    "/^engvar[ab]$/",
    "/^use[\sa-z]+(english|spelling|referencing)$/",
    "/^use [dmy]+ dates$/",
    "/^wikipedia articles (for|with|needing|containing).*$/",
    "/^(.*-)?stub$/"
];

function remove_templates($text)
{
    global $tempsToDelete, $temps_patterns;
    // ---
    $pattern = "/\{{2}((?>[^\{\}]+)|(?R))*\}{2}/x";
    preg_match_all($pattern, $text, $matches);
    // ---
    // echo "<pre>";
    // echo htmlentities(var_export($matches, true));
    // echo "</pre><br>";
    // ---
    foreach ($matches[0] as $text_template) {
        // ---
        $temp = getTemplate($text_template);
        // ---
        $name = $temp->getName();
        // ---
        $name = trim(strtolower(str_replace('_', ' ', $name)));
        // ---
        if (in_array($name, $tempsToDelete)) {
            $text = str_replace($text_template, '', $text);
            continue;
        }
        // ---
        // if $name start with "#unlinkedwikibase" delete it
        if (strpos($name, "#unlinkedwikibase") === 0) {
            $text = str_replace($text_template, '', $text);
            continue;
        }
        // ---
        foreach ($temps_patterns as $pattern) {
            if (preg_match($pattern, $name)) {
                $text = str_replace($text_template, '', $text);
                continue;
            }
        }
        // ---
    }
    // ---
    return trim($text);
}

function remove_templates_lead($text)
{
    // ---
    // remove any thig before {{Infobox medical condition
    $temps = [
        "{{infobox",
        "{{drugbox",
        "{{speciesbox",
    ];
    // ---
    $text2 = strtolower($text);
    // ---
    foreach ($temps as $temp) {
        $temp_index = strpos($text2, strtolower($temp));
        // ---
        if ($temp_index !== false) {
            $text = substr($text, $temp_index);
            break;
        }
    }

    return trim($text);
}

function add_missing_title($text, $title)
{
    $pattern = "/\{{2}((?>[^\{\}]+)|(?R))*\}{2}/x";
    preg_match_all($pattern, $text, $matches);
    // ---
    $temps = [
        "drugbox" => "drug_name",
        "infobox drug" => "drug_name",
        "infobox medical condition" => "name",
        "infobox medical intervention" => "name",

    ];
    // ---
    foreach ($matches[0] as $text_template) {
        // ---
        $temp = getTemplate($text_template);
        // ---
        $temp_name = $temp->getName();
        // ---
        $name = trim(strtolower(str_replace('_', ' ', $temp_name)));
        // ---
        if (isset($temps[$name])) {
            $param = $temps[$name];
            echo "$name: $param";
            // ---
            $name_p = $temp->getParameter($param);
            if (!$name_p || trim($name_p) == "") {
                // $temp->setParameter($param, $title);
                // $new_temp = $temp->toString(true);
                // ---
                $new_temp = str_replace('{{' . $temp_name, '{{' . $temp_name . "| $param = $title\n", $text_template);
                // ---
                $text = str_replace($text_template, $new_temp, $text);
            }
        }
    }
    return trim($text);
}
