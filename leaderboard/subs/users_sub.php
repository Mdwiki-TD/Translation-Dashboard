<?php

namespace Leaderboard\Subs\SubUsers;

/*
Usage:
use function Leaderboard\Subs\SubUsers\get_users_tables;

*/

use function SQLorAPI\Funcs\get_user_views;
use function SQLorAPI\Funcs\get_user_pages;
use function SQLorAPI\Process\get_user_process_new;
use function Leaderboard\Subs\LeadHelp\make_key;

function add_inp($dd_Pending, $user, $year_y)
{
    // ---
    $to_add = get_user_process_new($user, $year_y);
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
    $dd = [];
    $dd_Pending = [];
    //---
    $sql_result = get_user_pages($user_main, $year_y, $lang_y);
    //---
    foreach ($sql_result as $yhu => $tabb) {
        //---
        $kry = make_key($tabb);
        //---
        if (!empty($tabb['target'] ?? '')) {
            $dd[$kry] = $tabb;
        } else {
            $dd_Pending[$kry] = $tabb;
        };
    };
    //---
    return ['dd' => $dd, 'dd_Pending' => $dd_Pending];
}

function get_users_tables($mainuser, $year_y, $lang_y)
{
    // ---
    $result = ['dd' => [], 'dd_Pending' => [], 'table_of_views' => []];
    // ---
    if (empty($mainuser)) {
        return $result;
    };
    //---
    $user_main = $mainuser;
    $user_main = rawurldecode(str_replace('_', ' ', $user_main));
    //---
    $p_tables = pages_tables($user_main, $year_y, $lang_y);
    //---
    $dd = $p_tables['dd'];
    $dd_Pending = $p_tables['dd_Pending'];
    //---
    $dd_Pending = add_inp($dd_Pending, $user_main, $year_y);
    //---
    krsort($dd);
    //---
    krsort($dd_Pending);
    //---
    $table_of_views = []; //get_user_views($user_main, $year_y, $lang_y);
    //---
    $result['dd'] = $dd;
    $result['dd_Pending'] = $dd_Pending;
    $result['table_of_views'] = $table_of_views;
    //---
    return $result;
}
