<?php

namespace Leaderboard\SubUsers;

/*
Usage:
use function Leaderboard\SubUsers\get_users_tables;

*/

use function Actions\MdwikiSql\fetch_query;

function views_by_page_new($user_main, $test = '')
{
    //---
    $table_of_views = array();
    //---
    $views_qua = <<<SQL
        select p.target, v.countall
        from pages p, views v
        where p.user = ?
        and p.lang = v.lang
        and p.target = v.target
    SQL;
    //---
    $views_params = [$user_main];
    //---
    if (!empty($test)) {
        echo $views_qua . '<br>';
    }
    //--
    $views_query = fetch_query($views_qua, $views_params);
    //---
    foreach ($views_query as $Key => $table) {
        $targ = $table['target'] ?? "";
        $table_of_views[$targ] = $table['countall'] ?? "";
        //---
    };
    //---
    return $table_of_views;
}

function views_tables($user_main, $year_y, $lang_y, $test = '')
{
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
    if (!empty($test)) {
        echo $count_sql . '<br>';
    }
    //--
    $count_query = fetch_query($count_sql, $params);
    //---
    $user_count = $count_query[0]['count'];
    //---
    unset($count_query);
    //---
    if (!empty($test)) echo "<br>user_count : $user_count<br>";
    //---
    $table_of_views = views_by_page_new($user_main, $test = $test);
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
    if (!empty($test)) {
        echo $pages_qua . '<br>';
    }
    //---
    $sql_result = fetch_query($pages_qua, $params);
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
    $table_of_views = views_tables($user_main, $year_y, $lang_y, $test = '');
    //---
    $pages_table = pages_tables($user_main, $year_y, $lang_y, $test = '');
    //---
    $dd = $pages_table['dd'];
    $dd_Pending = $pages_table['dd_Pending'];
    //---
    return array('dd' => $dd, 'dd_Pending' => $dd_Pending, 'table_of_views' => $table_of_views);
}
