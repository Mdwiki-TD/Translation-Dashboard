<?php

namespace Leaderboard\SubUsers;

/*
Usage:
use function Leaderboard\SubUsers\get_users_tables;

*/

use function Actions\MdwikiSql\execute_query;

function get_users_tables($mainuser, $year_y, $lang_y, $test = '')
{
    $user_main = $mainuser;
    $user_main = rawurldecode(str_replace('_', ' ', $user_main));
    //---
    $dd = array();
    $dd_Pending = array();
    $table_of_views = array();
    //---
    $pages_qua = <<<SQL
        select * from pages where user = '$user_main'
    SQL;
    //---
    $count_sql = <<<SQL
        select count(title) as count from pages where user = '$user_main'
    SQL;
    //---
    if ($lang_y != 'All') {
        $count_sql .= " and lang = '$lang_y'";
        $pages_qua .= " and lang = '$lang_y'";
    };
    //---
    if ($year_y != 'All') {
        $count_sql .= " and YEAR(date) = '$year_y'";
        $pages_qua .= " and YEAR(date) = '$year_y'";
    };
    //---
    $views_qua = <<<SQL
        select p.target, v.countall
        from pages p, views v
        where p.user = '$user_main'
        and p.lang = v.lang
        and p.target = v.target
        limit 200
    SQL;
    //---
    if ($test != '') {
        echo $count_sql . '<br>';
        echo $pages_qua . '<br>';
        echo $views_qua . '<br>';
    }
    //---
    //---
    if ($mainuser != '') {
        //---
        $count_query = execute_query($count_sql);
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
            $views_query = execute_query($quaa_view);
            //---
            if (count($views_query) == 0) $done = $user_count;
            //---
            foreach ($views_query as $Key => $table) {
                $countall = $table['countall'] ?? "";
                $targ = $table['target'] ?? "";
                $table_of_views[$targ] = $countall;
                //---
                $done += 1;
                //---
            };
            //---
            unset($views_query);
            //---
            $offset += 200;
            //---
        };
        //---
        $sql_result = execute_query($pages_qua);
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
    };
    //---
    return array('dd' => $dd, 'dd_Pending' => $dd_Pending, 'table_of_views' => $table_of_views);
}
