<?php

namespace Leaderboard\Subs\SubUsers;

/*
Usage:
use function Leaderboard\Subs\SubUsers\get_users_tables;

*/

use function SQLorAPI\Get\get_user_views;
use function SQLorAPI\Get\get_user_pages;
use function SQLorAPI\Get\get_inprocess_user_new;
use function Leaderboard\Subs\LeadHelp\make_key;

function add_inp($dd_Pending, $user)
{
    // ---
    $to_add = get_inprocess_user_new($user);
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

function pages_tables($user_main, $year_y, $lang_y)
{
    //---
    $sql_result = get_user_pages($user_main, $year_y, $lang_y);
    //---
    $dd = [];
    $dd_Pending = [];
    //---
    foreach ($sql_result as $tait => $tabb) {
        //---
        $kry = make_key($tabb);
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

function get_users_tables($mainuser, $year_y, $lang_y)
{
    $user_main = $mainuser;
    $user_main = rawurldecode(str_replace('_', ' ', $user_main));
    //---
    if (empty($mainuser)) {
        return array('dd' => [], 'dd_Pending' => [], 'table_of_views' => []);
    };
    //---
    $table_of_views = []; //get_user_views($user_main, $year_y, $lang_y);
    //---
    $pages_table = pages_tables($user_main, $year_y, $lang_y);
    //---
    $dd = $pages_table['dd'];
    $dd_Pending = $pages_table['dd_Pending'];
    //---
    $dd_Pending = add_inp($dd_Pending, $user_main);
    //---
    return array('dd' => $dd, 'dd_Pending' => $dd_Pending, 'table_of_views' => $table_of_views);
}
