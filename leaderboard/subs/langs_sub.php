<?php

namespace Leaderboard\SubLangs;

/*
Usage:
use function Leaderboard\SubLangs\get_langs_tables;

*/

use function SQLorAPI\Get\get_lang_views;
use function SQLorAPI\Get\get_lang_pages;
use function SQLorAPI\Get\get_inprocess_lang_new;
use function Leaderboard\LeadHelp\make_key;

function add_inp($dd_Pending, $mainlang)
{
    // ---
    $to_add = get_inprocess_lang_new($mainlang);
    // ---
    foreach ($to_add as $_ => $Taab) {
        //---
        $kry = make_key($Taab);
        //---
        if (!in_array($kry, array_keys($dd_Pending))) {
            $dd_Pending[$kry] = $Taab;
        };
    };
    //---
    return $dd_Pending;
}

function pages_tables($mainlang, $year_y)
{
    //---
    $dd = [];
    $dd_Pending = [];
    //---
    $rrr2 = get_lang_pages($mainlang, $year_y);
    //---
    foreach ($rrr2 as $yhu => $Taab) {
        //---
        $kry = make_key($Taab);
        //---
        if (!empty($Taab['target'])) {
            $dd[$kry] = $Taab;
        } else {
            $dd_Pending[$kry] = $Taab;
        };
    };
    //---
    return array('dd' => $dd, 'dd_Pending' => $dd_Pending);
}

function get_langs_tables($mainlang, $year_y)
{
    //---
    $table_of_views = []; //get_lang_views($mainlang, $year_y);
    //---
    $p_tables = pages_tables($mainlang, $year_y);
    //---
    $dd = $p_tables['dd'];
    $dd_Pending = $p_tables['dd_Pending'];
    //---
    $dd_Pending = add_inp($dd_Pending, $mainlang);
    //---
    return array('dd' => $dd, 'dd_Pending' => $dd_Pending, 'table_of_views' => $table_of_views);
}
