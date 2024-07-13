<?php
namespace EnWiki\Fixes\ExpendRefs;

/*
Usage:

use function EnWiki\Fixes\ExpendRefs\refs_expend_work;

*/

use function WikiParse\Citations\get_full_refs;
use function WikiParse\Citations\getShortCitations;

function refs_expend_work($first, $alltext)
{
    if ($alltext == "") {
        $alltext = $first;
    }
    $refs = get_full_refs($alltext);
    // echo  "refs:" . count($refs) . "<br>";

    $short_refs = getShortCitations($first);
    // echo  "short_refs:" . count($short_refs) . "<br>";

    foreach ($short_refs as $cite) {
        $name = $cite["name"];
        $refe = $cite["tag"];
        // ---
        $rr = $refs[$name] ?? false;
        if ($rr) {
            $first = str_replace($refe, $rr, $first);
        }
    }
    return $first;
}
