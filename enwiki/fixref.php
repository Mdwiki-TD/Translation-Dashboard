<?php
// fixref.php

$example = array(
    0 =>
    array(
        0 => '<ref name=ILAE2023>{{cite web |title=Dravet syndrome |url=https://www.epilepsydiagnosis.org/syndrome/dravet-overview.html |website=www.epilepsydiagnosis.org}}</ref>',
    ),
    1 =>
    array(
        0 => 'ILAE2023',
    ),
    2 =>
    array(
        0 => '{{cite web |title=Dravet syndrome |url=https://www.epilepsydiagnosis.org/syndrome/dravet-overview.html |website=www.epilepsydiagnosis.org}}',
    ),
);
//---
$short_example = array (
    0 => 
    array (
        0 => '<ref name=Lh2013/>',
        1 => '<ref name=NORD2023/>',
    ),
    1 => 
    array (
        0 => 'Lh2013',
        1 => 'NORD2023',
    ),
);
//---
function fix_ref($first, $alltext) {
    // ---
    $ref_complite = '/<ref(\s*name\s*\=*\s*[\"\']*([^>]*)[\"\']*\s*)>[^<>]+<\/ref>/';
    //---
    // $ref_short = '/<ref\s*name\s*\=\s*[\"\']*([^>]*)[\"\']*\s*\/\s*>/';
    //---
    // test_print('<br>------------- fix_ref -------------<br>');
    preg_match_all($ref_complite, $alltext, $matches);
    //---
    // echo "<pre>";
    // echo htmlentities(var_export($matches, true));
    // echo "</pre>";
    //---
    foreach ($matches[0] as $key => $full_ref) {
        $ref_name = trim($matches[2][$key]);
        // if $ref_name has one of (',") in the start ot the end : remove it
        $ref_name = preg_replace('/\s*["\']$/', '', $ref_name);
        $ref_name = preg_replace('/^\s*["\']/', '', $ref_name);
        $ref_name = trim($ref_name);
        //---
        if ($ref_name != '') {
            //---
            // test_print('--------------------------<br>');
            //---
            $firstx = preg_replace("/<ref\s*name\s*=\s*[\"']*\s*" . $ref_name . "\s*[\"']*\s*\/\s*>/", $full_ref, $first);
            //---
            // test_print("name\t:($ref_name)<br>");
            // test_print("ref\t:" . htmlentities($full_ref) . "<br>");
            //---
            if ($firstx) {
                $first = $firstx;
            }
            //---
        }
    }
    // ---
    return $first;
}
