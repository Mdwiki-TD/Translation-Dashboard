<?PHP
//---
/*
include_once 'sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
*/
//---
include_once 'functions.php';
//---
$full_translates = [];
$no_lead_translates = [];
//---
$translate_type_sql = <<<SQL
    SELECT tt_title, tt_lead, tt_full
	FROM translate_type
SQL;
//---
foreach ( execute_query($translate_type_sql) AS $k => $tab ) {
    // if tt_full == 1 then add tt_title to $full_translates
    if ($tab['tt_full'] == 1) {
        $full_translates[] = $tab['tt_title'];
    }
    if ($tab['tt_lead'] == 0) {
        $no_lead_translates[] = $tab['tt_title'];
    }
}
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

function make_views_by_lang_target() {
    $vta = array();

    $qua_vi = "
    SELECT target, lang, countall, count2021, count2022, count2023, count2024, count2025, count2026
    FROM views;
    ";

    foreach (execute_query($qua_vi) as $k => $tab) {
        // check if lang already in array array_key_exists
        $langcode = $tab['lang'];
        $target   = $tab['target'];
		if (!array_key_exists($langcode, $vta)) {
			$vta[$langcode] = [];
		};
        // add to array
        $vta[$langcode][$target] = array(
            'all'  => $tab['countall'],
            '2021' => $tab['count2021'],
            '2022' => $tab['count2022'],
            '2023' => $tab['count2023'],
            '2024' => $tab['count2024'],
            '2025' => $tab['count2025'],
            '2026' => $tab['count2026'],
        );
    }
    return $vta;
}
//---
?>