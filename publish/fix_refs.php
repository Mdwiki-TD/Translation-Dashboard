<?php
namespace Publish\FixRefs;

/*
Usage:
include_once __DIR__ . '/../publish/fix_refs.php';
use function Publish\FixRefs\fix_wikirefs;
*/

include_once __DIR__ . '/../../fixwikirefs/fix.php';

function endsWith($string, $endString)
{
    $len = strlen($endString);
    return substr($string, -$len) === $endString;
};

function fix_wikirefs($wikitext, $lang)
{
    // ---
    $resultb = get_text_results($wikitext, $lang);
    // ---
    $resultb = $resultb['output'] ?? '';
    // ---
    $resultb = trim($resultb);
    // ---
    $t3 = endsWith($resultb, '.txt');
    //---
    if ($t3) {
        $newtext = file_get_contents($resultb);
        if (!empty($newtext)) {
            return $newtext;
        };
    };
    //---
    return $wikitext;
}
