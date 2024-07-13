<?php
namespace WikiParse\Citations;

/*
Usage:

use function WikiParse\Citations\get_name;
use function WikiParse\Citations\getCitations;
use function WikiParse\Citations\get_full_refs;
use function WikiParse\Citations\getShortCitations;

*/

// include_once __DIR__ . '/../WikiParse/Citations.php';

/**
 * Get all the citations from the provided text and parse them into an array.
 *
 * @param string $text The text containing citations to extract
 * @return array Array of citation information including content, tag, and options
 */

function get_name($options)
{
    if (trim($options) == "") {
        return "";
    }
    // $pa = "/name\s*=\s*\"(.*?)\"/i";
    $pa = "/name\s*\=\s*[\"\']*([^>\"\']*)[\"\']*\s*/i";
    preg_match($pa, $options, $matches);
    // ---
    if (!isset($matches[1])) {
        return "";
    }
    $name = trim($matches[1]);
    return $name;
}
function getCitations($text)
{
    preg_match_all("/<ref([^\/>]*?)>(.+?)<\/ref>/is", $text, $matches);
    // ---
    $citations = [];
    // ---
    foreach ($matches[1] as $key => $citation_options) {
        $content = $matches[2][$key];
        $ref_tag = $matches[0][$key];
        $options = $citation_options;
        $citation = [
            "content" => $content,
            "tag" => $ref_tag,
            "name" => get_name($options),
            "options" => $options
        ];
        $citations[] = $citation;
    }

    return $citations;
}

function get_full_refs($text)
{
    $full = [];
    $citations = getCitations($text);
    // ---
    foreach ($citations as $cite) {
        $name = $cite["name"];
        $ref = $cite["tag"];
        // ---
        $full[$name] = $ref;
    };
    // ---
    return $full;
}

function getShortCitations($text)
{
    preg_match_all("/<ref([^\/>]*?)\/\s*>/is", $text, $matches);
    // ---
    $citations = [];
    // ---
    foreach ($matches[1] as $key => $citation_options) {
        $ref_tag = $matches[0][$key];
        $options = $citation_options;
        $citation = [
            "content" => "",
            "tag" => $ref_tag,
            "name" => get_name($options),
            "options" => $options
        ];
        $citations[] = $citation;
    }

    return $citations;
}
