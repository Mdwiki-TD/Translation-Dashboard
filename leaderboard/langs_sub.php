<?php

namespace Leaderboard\SubLangs;

/*
Usage:
use function Leaderboard\SubLangs\get_langs_tables;

*/

use function Actions\MdwikiSql\execute_query;

function get_langs_tables($mainlang, $year_y)
{
    //---
    $dd = array();
    $dd_Pending = array();
    $table_of_views = array();
    //---
    // views (target, countall, count2021, count2022, count2023, lang)
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
    $views_quary = execute_query($qua_views);
    //---
    foreach ($views_quary as $Key => $t) $table_of_views[$t['target']] = $t['countall'] ?? "";
    //---
    $pages_qua = <<<SQL
        select * from pages where lang = '$mainlang'
    SQL;
    //---
    if ($year_y != 'All') {
        $pages_qua .= " and YEAR(date) = '$year_y'";
    };
    //---
    foreach (execute_query($pages_qua) as $yhu => $Taab) {
        //---
        $dat1 = $Taab['pupdate'] ?? '';
        $dat2 = $Taab['date'] ?? '';
        $dat = ($dat1 != '') ? $dat1 : $dat2;
        //---
        $urt = '';
        if ($dat != '') {
            $urt = str_replace('-', '', $dat) . ':';
        };
        $kry = $urt . $Taab['lang'] . ':' . $Taab['title'];
        //---
        if ($Taab['target'] != '') {
            $dd[$kry] = $Taab;
        } else {
            $dd_Pending[$kry] = $Taab;
        };
        //---
    };
    //---
    return array('dd' => $dd, 'dd_Pending' => $dd_Pending, 'table_of_views' => $table_of_views);
}
