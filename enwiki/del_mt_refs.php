<?php

include_once __DIR__ . '/../WikiParse/Citations.php';

use function WikiParse\Citations\get_full_refs;
use function WikiParse\Citations\getShortCitations;

function del_empty_refs($first)
{
    // ---
    $refs = get_full_refs($first);
    // echo  "refs:" . count($refs) . "<br>";

    $short_refs = getShortCitations($first);
    // echo  "short_refs:" . count($short_refs) . "<br>";

    foreach ($short_refs as $cite) {
        $name = $cite["name"];
        $refe = $cite["tag"];
        // ---
        $rr = isset($refs[$name]) ? $refs[$name] : false;
        if ($rr) {
            // if $rr already in $first : continue
            if (strpos($first, $rr) === false) {
                $first = str_replace($refe, $rr, $first);
            }
        } else {
            $first = str_replace($refe, "", $first);
        }
    }
    return $first;
}
