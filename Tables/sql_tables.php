<?PHP
//---
/*
include_once __DIR__ . '/Tables/sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
*/
//---
include_once __DIR__ . '/../actions/functions.php';
include_once __DIR__ . '/../api_or_sql/index.php';
//---
use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;
use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function SQLorAPI\GetDataTab\get_td_or_sql_categories;
use function SQLorAPI\GetDataTab\get_td_or_sql_projects;
use function SQLorAPI\GetDataTab\get_td_or_sql_settings;
use function SQLorAPI\GetDataTab\get_td_or_sql_views;

//---
$full_translates = [];
$no_lead_translates = [];
//---
$rere = get_td_or_sql_translate_type();
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
$full_t = get_td_or_sql_full_translators();
//---
// $full_translators = array_map(function ($row) {return $row['user']; }, $full_t);
$full_translators = array_column($full_t, 'user');
//---
$sql_qids = get_td_or_sql_qids();
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
$categories_tab = get_td_or_sql_categories();
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
$projects_tab = get_td_or_sql_projects();
//---
foreach ($projects_tab as $Key => $table) $projects_title_to_id[$table['g_title']] = $table['g_id'];
//---
function make_views_by_lang_target()
{
    $vta = array();
    // ---
    $tat = get_td_or_sql_views();
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
$settings = array();
//---
$settings_tab = get_td_or_sql_settings();
//---
foreach ($settings_tab as $Key => $table) {
    $settings[$table['title']] = $table;
}
// ---
