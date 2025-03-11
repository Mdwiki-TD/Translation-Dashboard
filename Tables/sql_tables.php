<?PHP
//---
namespace Tables\SqlTables;
//---
/*
use function Tables\SqlTables\load_translate_type;
use function Tables\SqlTables\make_views_by_lang_target;

include_once __DIR__ . '/Tables/sql_tables.php'; // $cat_titles $cat_to_camp $camp_to_cat
*/
//---
include_once __DIR__ . '/../actions/test_print.php';
include_once __DIR__ . '/../actions/functions.php';
include_once __DIR__ . '/../api_or_sql/index.php';
//---
use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;
use function SQLorAPI\GetDataTab\get_td_or_sql_categories;
use function SQLorAPI\GetDataTab\get_td_or_sql_settings;
use function SQLorAPI\GetDataTab\get_td_or_sql_views;

//---
$full_translates = [];
$no_lead_translates = [];
//---
function load_translate_type($ty)
{
    global $full_translates, $no_lead_translates;
    // ---
    if (empty($full_translates)) {
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
    }
    // ---
    $tab = ($ty == 'full') ? $full_translates : $no_lead_translates;
    return $tab;
}
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
function make_views_by_lang_target($year, $lang)
{
    $vta = array();
    // ---
    $tat = get_td_or_sql_views($year, $lang);
    // ---
    foreach ($tat as $k => $tab) {
        // check if lang already in array array_key_exists
        $langcode = $tab['lang'];
        $target   = $tab['target'];
        if (!array_key_exists($langcode, $vta)) {
            $vta[$langcode] = [];
        };
        // add to array
        $vta[$langcode][$target] = $tab['countall'];
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
