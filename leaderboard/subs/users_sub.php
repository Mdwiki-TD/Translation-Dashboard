<?php

namespace Leaderboard\SubUsers;

/*
Usage:
use function Leaderboard\SubUsers\get_users_tables;

*/

use function Actions\MdwikiSql\execute_query;

function views_tables($user_main, $year_y, $lang_y, $test = '')
{
    //---
    $table_of_views = array();
    //---
    $count_sql = <<<SQL
        select count(title) as count from pages where user = ?
    SQL;
    //---
    $params = [$user_main];
    //---
    if ($lang_y != 'All') {
        $count_sql .= " and lang = ?";
        $params[] = $lang_y;
    };
    //---
    if ($year_y != 'All') {
        $count_sql .= " and YEAR(date) = ?";
        $params[] = $year_y;
    };
    //---
    $views_qua = <<<SQL
        select p.target, v.countall
        from pages p, views v
        where p.user = ?
        and p.lang = v.lang
        and p.target = v.target
        limit 200
    SQL;
    //---
    $views_params = [$user_main];
    //---
    if ($test != '') {
        echo $count_sql . '<br>';
        echo $views_qua . '<br>';
    }
    //--
    $count_query = execute_query($count_sql, $params);
    //---
    $user_count = $count_query[0]['count'];
    //---
    unset($count_query);
    //---
    if ($test != '') echo "<br>user_count : $user_count<br>";
    //---
    $done = 0;
    $offset = 0;
    //---
    while ($done < $user_count) {
        //---
        $quaa_view = $views_qua;
        $quaa_view .= "
            offset $offset
        ";
        //---
        $views_query = execute_query($quaa_view, $views_params);
        //---
        if (count($views_query) == 0) $done = $user_count;
        //---
        foreach ($views_query as $Key => $table) {
            $countall = $table['countall'] ?? "";
            $targ = $table['target'] ?? "";
            $table_of_views[$targ] = $countall;
            //---
            $done += 1;
        };
        //---
        unset($views_query);
        //---
        $offset += 200;
        //---
    };
    //---
    return $table_of_views;
}

function pages_tables($user_main, $year_y, $lang_y, $test = '')
{
    //---
    $dd = array();
    $dd_Pending = array();
    //---
    $pages_qua = <<<SQL
        select * from pages where user = ?
    SQL;
    //---
    $params = [$user_main];
    //---
    if ($lang_y != 'All') {
        $pages_qua .= " and lang = ?";
        $params[] = $lang_y;
    };
    //---
    if ($year_y != 'All') {
        $pages_qua .= " and YEAR(date) = ?";
        $params[] = $year_y;
    };
    //---
    if ($test != '') {
        echo $pages_qua . '<br>';
    }
    //---
    $sql_result = execute_query($pages_qua, $params);
    //---
    foreach ($sql_result as $tait => $tabb) {
        //---
        $kry = str_replace('-', '', $tabb['pupdate']) . ':' . $tabb['lang'] . ':' . $tabb['title'];
        //---
        if ($tabb['target'] != '') {
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
    if ($mainuser == '') {
        return array('dd' => [], 'dd_Pending' => [], 'table_of_views' => []);
    };
    //---
    $table_of_views = views_tables($user_main, $year_y, $lang_y, $test = '');
    //---
    $pages_table = pages_tables($user_main, $year_y, $lang_y, $test = '');
    //---
    $dd = $pages_table['dd'];
    $dd_Pending = $pages_table['dd_Pending'];
    //---
    return array('dd' => $dd, 'dd_Pending' => $dd_Pending, 'table_of_views' => $table_of_views);
}
