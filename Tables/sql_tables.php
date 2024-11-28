<?PHP
//---
/*
include_once __DIR__ . '/Tables/sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
*/
//---
include_once __DIR__ . '/../actions/functions.php';
//---
// use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;
//---
$full_translates = [];
$no_lead_translates = [];
//---
$translate_type_sql = "SELECT tt_title, tt_lead, tt_full FROM translate_type";
//---
// $rere = fetch_query ($translate_type_sql);
$rere = get_td_api(array('get' => 'translate_type'));
//---
foreach ($rere as $k => $tab) {
    // if tt_full == 1 then add tt_title to $full_translates
    if ($tab['tt_full'] == 1) {
        $full_translates[] = $tab['tt_title'];
    }
    if ($tab['tt_lead'] == 0) {
        $no_lead_translates[] = $tab['tt_title'];
    }
}
//---
// $full_t = fetch_query ("SELECT * FROM full_translators");
$full_t = get_td_api(array('get' => 'full_translators'));
//---
$full_translators = array_map(function ($row) {
    return $row['user'];
}, $full_t);
//---
$sql_qids = array();
//---
// $qids_t = fetch_query ('select title, qid from qids;');
$qids_t = get_td_api(array('get' => 'qids'));
//---
foreach ($qids_t as $k => $tab) $sql_qids[$tab['title']] = $tab['qid'];
//---
$cat_titles = array();
$cat_to_camp = array();
$camp_to_cat = array();
//---
$main_cat = ''; # RTT
$main_camp = ''; # Main
//---
$camps_cat2 = array();
$camp_input_depth = array();
// $catinput_depth = array();
//---
$campaign_input_list = array();
$catinput_list = array();
//---
// $categories_tab = fetch_query ('select id, category, category2, campaign, depth, def from categories;');
$categories_tab = get_td_api(array('get' => 'categories'));
//---
foreach ($categories_tab as $k => $tab) {
    if (!empty($tab['category']) && !empty($tab['campaign'])) {
        //---
        $cat_titles[] = $tab['campaign'];
        //---
        $camps_cat2[$tab['campaign']] = $tab['category2'];
        //---
        $cat_to_camp[$tab['category']] = $tab['campaign'];
        $camp_to_cat[$tab['campaign']] = $tab['category'];
        //---
        $catinput_list[$tab['category']] = $tab['category'];
        $campaign_input_list[$tab['campaign']] = $tab['campaign'];
        // ---
        // $catinput_depth[$tab['category']] = $tab['depth'];
        $camp_input_depth[$tab['campaign']] = $tab['depth'];
        //---
        $default  = $tab['def'];
        if ($default == 1 || $default == '1') $main_cat = $tab['category'];
        if ($default == 1 || $default == '1') $main_camp = $tab['campaign'];
        //---
    };
};
//---
$projects_title_to_id = array();
//---
// $projects_tab = fetch_query ('select g_id, g_title from projects;');
$projects_tab = get_td_api(array('get' => 'projects'));
//---
foreach ($projects_tab as $Key => $table) $projects_title_to_id[$table['g_title']] = $table['g_id'];
//---
$settings = array();
//---
// $settings_tab = fetch_query ('select id, title, displayed, value, Type from settings;');
$settings_tab = get_td_api(array('get' => 'settings'));
//---
foreach ($settings_tab as $Key => $table) {
    $settings[$table['title']] = $table;
}
//---

function make_views_by_lang_target()
{
    $vta = array();

    $qua_vi = "
    SELECT target, lang, countall, count2021, count2022, count2023, count2024, count2025, count2026
    FROM views;
    ";
    // ---
    // $tat = fetch_query ($qua_vi);
    $tat = get_td_api(array('get' => 'views'));
    // ---
    foreach ($tat as $k => $tab) {
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
