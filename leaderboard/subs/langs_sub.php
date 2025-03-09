<?php

namespace Leaderboard\SubLangs;

/*
Usage:
use function Leaderboard\SubLangs\get_langs_tables;

*/

use function SQLorAPI\Get\get_lang_views;
use function SQLorAPI\Get\get_lang_pages;

function pages_tables($mainlang, $year_y)
{
    //---
    $dd = array();
    $dd_Pending = array();
    //---
    $rrr2 = get_lang_pages($mainlang, $year_y);
    //---
    foreach ($rrr2 as $yhu => $Taab) {
        //---
        $dat1 = $Taab['pupdate'] ?? '';
        $dat2 = $Taab['date'] ?? '';
        $dat = (!empty($dat1)) ? $dat1 : $dat2;
        //---
        $urt = '';
        if (!empty($dat)) {
            $urt = str_replace('-', '', $dat) . ':';
        };
        $kry = $urt . $Taab['lang'] . ':' . $Taab['title'];
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
    $uux = get_lang_views($mainlang, $year_y);
    //---
    $table_of_views = [];
    // ---
    foreach ($uux as $Key => $table) {
        $targ = $table['target'] ?? "";
        $table_of_views[$targ] = $table['countall'] ?? "";
    };
    //---
    $p_tables = pages_tables($mainlang, $year_y);
    //---
    $dd = $p_tables['dd'];
    $dd_Pending = $p_tables['dd_Pending'];
    //---
    return array('dd' => $dd, 'dd_Pending' => $dd_Pending, 'table_of_views' => $table_of_views);
}
