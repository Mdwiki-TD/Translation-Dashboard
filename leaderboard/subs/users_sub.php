<?php

namespace Leaderboard\SubUsers;

/*
Usage:
use function Leaderboard\SubUsers\get_users_tables;

*/

use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;
use function Actions\TDApi\compare_it;

function views_by_page($user_main, $user_count, $test = '')
{

    $table_of_views = array();
    //---
    $done = 0;
    $offset = 0;
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
    while ($done < $user_count) {
        //---
        $quaa_view = $views_qua;
        // ---
        if ($offset > 0) {
            $quaa_view .= "
                offset $offset
            ";
        }
        //---
        $views_query = fetch_query ($quaa_view, $views_params);
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

function views_by_page_new($user_main, $user_count, $test = '')
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
    $views_query = fetch_query ($views_qua, $views_params);
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
    $api_params = ['get' => 'pages', 'user' => $user_main, 'select' => 'count(title) as count'];
    //---
    if ($lang_y != 'All') {
        $count_sql .= " and lang = ?";
        $params[] = $lang_y;
        $api_params['lang'] = $lang_y;
    };
    //---
    if ($year_y != 'All') {
        $count_sql .= " and YEAR(date) = ?";
        $params[] = $year_y;
        $api_params['YEAR(date)'] = $year_y;
    };
    //---
    if (!empty($test)) {
        echo $count_sql . '<br>';
    }
    //--
    $count_query = fetch_query ($count_sql, $params);
    //---
    $user_count = $count_query[0]['count'];
    //---
    unset($count_query);
    //---
    if (!empty($test)) echo "<br>user_count : $user_count<br>";
    //---
    $table_of_views = views_by_page_new($user_main, $user_count, $test = $test);
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
    $api_params = ['get' => 'pages', 'user' => $user_main];
    //---
    if ($lang_y != 'All') {
        $pages_qua .= " and lang = ?";
        $params[] = $lang_y;
        $api_params['lang'] = $lang_y;
    };
    //---
    if ($year_y != 'All') {
        $pages_qua .= " and YEAR(date) = ?";
        $params[] = $year_y;
        $api_params['YEAR(date)'] = $year_y;
    };
    //---
    if (!empty($test)) {
        echo $pages_qua . '<br>';
    }
    //---
    // $sql_result = fetch_query ($pages_qua, $params);
    $sql_result = get_td_api($api_params);
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
    // $table_of_views = views_tables($user_main, $year_y, $lang_y, $test = '');
    //---
    $uux = get_td_api(['get' => 'user_views', 'user' => $user_main]);
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
