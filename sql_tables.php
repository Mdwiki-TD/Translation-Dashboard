<?PHP
//---
/*
include_once('sql_tables.php'); // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
*/
//---
include_once('functions.php');
//---
$sql_qids = array();
//---
foreach ( execute_query('select title, qid from qids;') AS $k => $tab ) $sql_qids[$tab['title']] = $tab['qid'];
//---
$cat_titles = array();
$cat_to_camp = array();
$camp_to_cat = array();
//---
foreach ( execute_query('select id, category, display, depth from categories;') AS $k => $tab ) {
    if ($tab['category'] != '' && $tab['display'] != '') {
        $cat_titles[] = $tab['display'];
        $cat_to_camp[$tab['category']] = $tab['display'];
        $camp_to_cat[$tab['display']] = $tab['category']; 
    };
};
//---
$projects_title_to_id = array();
//---
foreach ( execute_query('select g_id, g_title from projects;') AS $Key => $table ) $projects_title_to_id[$table['g_title']] = $table['g_id'];
//---
$settings = array();
//---
foreach ( execute_query('select id, title, displayed, value, Type from settings;') AS $Key => $table ) {
    $settings[$table['title']] = $table;
}
//---

function make_views_by_target() {
    $vta = array();

    $qua_vi = "
    SELECT target, countall, count2021, count2022, count2023
    FROM views;
    ";

    foreach (execute_query($qua_vi) as $k => $tab) {
        $vta[$tab['target']] = array(
            'all'  => $tab['countall'],
            '2021' => $tab['count2021'],
            '2022' => $tab['count2022'],
            '2023' => $tab['count2023']
        );
    }
    return $vta;
}
//---
?>