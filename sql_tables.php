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
foreach ( execute_query_2('select title, qid from qids;') AS $k => $tab ) $sql_qids[$tab['title']] = $tab['qid'];
//---
$cat_titles = array();
$cat_to_camp = array();
$camp_to_cat = array();
//---
foreach ( execute_query_2('select id, category, display, depth from categories;') AS $k => $tab ) {
    if ($tab['category'] != '' && $tab['display'] != '') {
        $cat_titles[] = $tab['display'];
        $cat_to_camp[$tab['category']] = $tab['display'];
        $camp_to_cat[$tab['display']] = $tab['category']; 
    };
};
//---
//---
?>