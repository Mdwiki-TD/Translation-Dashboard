<?php

namespace Leaderboard\SubUsers;

/*
Usage:
use function Leaderboard\SubUsers\get_users_tables;

*/

use function Leaderboard\Get\get_user_views;
use function Leaderboard\Get\get_user_pages;

function pages_tables($user_main, $year_y, $lang_y, $test = '')
{
    //---
    $sql_result = get_user_pages($user_main, $year_y, $lang_y);
    //---
    $dd = array();
    $dd_Pending = array();
    //---
    foreach ($sql_result as $tait => $tabb) {
        //---
        $kry = str_replace('-', '', $tabb['pupdate']) . ':' . $tabb['lang'] . ':' . $tabb['title'];
        //---
        if (!empty($tabb['target'])) {
            $dd[$kry] = $tabb;
        } else {
            $dd_Pending[$kry] = $tabb;
        };
    };
    //---
    return array('dd' => $dd, 'dd_Pending' => $dd_Pending);
}

function get_users_tables($mainuser, $year_y, $lang_y, $test = '')
{
    $user_main = $mainuser;
    $user_main = rawurldecode(str_replace('_', ' ', $user_main));
    //---
    if (empty($mainuser)) {
        return array('dd' => [], 'dd_Pending' => [], 'table_of_views' => []);
    };
    //---
    $uux = get_user_views($user_main, $year_y, $lang_y);
    $uux2 = [];
    // ---
    foreach ($uux as $Key => $table) {
        $targ = $table['target'] ?? "";
        $uux2[$targ] = $table['countall'] ?? "";
    };
    //---
    // compare_it($table_of_views, $uux2);
    //---
    $pages_table = pages_tables($user_main, $year_y, $lang_y, $test = '');
    //---
    $dd = $pages_table['dd'];
    $dd_Pending = $pages_table['dd_Pending'];
    //---
    return array('dd' => $dd, 'dd_Pending' => $dd_Pending, 'table_of_views' => $uux2);
}
