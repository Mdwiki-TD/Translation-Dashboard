<?php

namespace Leaderboard\SubLangs;

/*
Usage:
use function Leaderboard\SubLangs\get_langs_tables;

*/

use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;
use function Actions\TDApi\compare_it;

function views_table_y($mainlang)
{
    //---
    $table_of_views = array();
    //---
    $qua_views = <<<SQL
    select
        #p.title, p.user, p.date, p.word, p.lang, p.cat, p.pupdate,
        p.target, v.countall

        from pages p, views v
        where p.lang = '$mainlang'
        and p.lang = v.lang
        and p.target = v.target
        ;
    SQL;
    //---
    $views_quary = fetch_query ($qua_views);
    //---
    foreach ($views_quary as $Key => $t) {
        $table_of_views[$t['target']] = $t['countall'] ?? "";
    };
    //---
    return $table_of_views;
}

function pages_tables($mainlang, $year_y)
{
    //---
    $dd = array();
    $dd_Pending = array();
    //---
    $pages_qua = <<<SQL
        select * from pages where lang = '$mainlang'
    SQL;
    //---
    $api_params = ['get' => 'pages', 'lang' => $mainlang];
    //---
    if ($year_y != 'All') {
        $pages_qua .= " and YEAR(date) = '$year_y'";
        $api_params['YEAR(date)'] = $year_y;
    };
    //---
    // $rrr = fetch_query ($pages_qua);
    $rrr2 = get_td_api($api_params);
    //---
    // compare_it($rrr, $rrr2);
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
    // $table_of_views_old = views_table_y($mainlang);
    //---
    $uux = get_td_api(['get' => 'lang_views', 'lang' => $mainlang]);
    $table_of_views = [];
    // ---
    foreach ($uux as $Key => $table) {
        $targ = $table['target'] ?? "";
        $table_of_views[$targ] = $table['countall'] ?? "";
    };
    //---
    // compare_it($table_of_views_old, $table_of_views);
    //---
    $p_tables = pages_tables($mainlang, $year_y);
    //---
    $dd = $p_tables['dd'];
    $dd_Pending = $p_tables['dd_Pending'];
    //---
    return array('dd' => $dd, 'dd_Pending' => $dd_Pending, 'table_of_views' => $table_of_views);
}
