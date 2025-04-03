<?PHP

namespace Tables\SqlTables;

//---
/*
(\$)(full_translates|no_lead_translates|cat_titles|cat_to_camp|camp_to_cat|main_cat|main_camp|camps_cat2|camp_input_depth|campaign_input_list|catinput_list)

TablesSql::$1s_$2

use Tables\SqlTables\TablesSql;
use function Tables\SqlTables\load_translate_type;
use function Tables\SqlTables\make_views_by_lang_target;

include_once __DIR__ . '/Tables/sql_tables.php'; // Tables::$s_cat_titles Tables::$s_cat_to_camp Tables::$s_camp_to_cat
*/
//---
include_once __DIR__ . '/../actions/test_print.php';
include_once __DIR__ . '/../actions/load_request.php';
include_once __DIR__ . '/../api_or_sql/index.php';
//---
use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;
use function SQLorAPI\GetDataTab\get_td_or_sql_categories;
use function SQLorAPI\GetDataTab\get_td_or_sql_settings;
use function SQLorAPI\GetDataTab\get_td_or_sql_views;
//---
class TablesSql
{
    public static $s_full_translates = [];
    public static $s_no_lead_translates = [];
    //---
    public static $s_cat_titles = [];
    public static $s_cat_to_camp = [];
    public static $s_camp_to_cat = [];
    //---
    public static $s_main_cat = ''; # RTT
    public static $s_main_camp = ''; # Main
    //---
    public static $s_camps_cat2 = [];
    public static $s_camp_input_depth = [];
    // public static $catinput_depth = [];
    //---
    public static $s_campaign_input_list = [];
    public static $s_catinput_list = [];
    public static $s_settings = [];
}


function load_translate_type($ty)
{
    if (empty(TablesSql::$s_full_translates)) {
        $rere = get_td_or_sql_translate_type();
        //---
        foreach ($rere as $k => $tab) {
            // if tt_full == 1 then add tt_title to TablesSql::$s_full_translates
            if ($tab['tt_full'] == 1) {
                TablesSql::$s_full_translates[] = $tab['tt_title'];
            }
            if ($tab['tt_lead'] == 0) {
                TablesSql::$s_no_lead_translates[] = $tab['tt_title'];
            }
        }
    }
    // ---
    $tab = ($ty == 'full') ? TablesSql::$s_full_translates : TablesSql::$s_no_lead_translates;
    // ---
    return $tab;
}
//---
$categories_tab = get_td_or_sql_categories();
//---
foreach ($categories_tab as $k => $tab) {
    if (!empty($tab['category']) && !empty($tab['campaign'])) {
        //---
        TablesSql::$s_cat_titles[] = $tab['campaign'];
        //---
        TablesSql::$s_camps_cat2[$tab['campaign']] = $tab['category2'];
        //---
        TablesSql::$s_cat_to_camp[$tab['category']] = $tab['campaign'];
        TablesSql::$s_camp_to_cat[$tab['campaign']] = $tab['category'];
        //---
        TablesSql::$s_catinput_list[$tab['category']] = $tab['category'];
        TablesSql::$s_campaign_input_list[$tab['campaign']] = $tab['campaign'];
        // ---
        // $catinput_depth[$tab['category']] = $tab['depth'];
        TablesSql::$s_camp_input_depth[$tab['campaign']] = $tab['depth'];
        //---
        $default  = $tab['def'];
        if ($default == 1 || $default == '1') TablesSql::$s_main_cat = $tab['category'];
        if ($default == 1 || $default == '1') TablesSql::$s_main_camp = $tab['campaign'];
        //---
    };
};

function make_views_by_lang_target($year, $lang)
{
    $vta = [];
    // ---
    $tat = get_td_or_sql_views($year, $lang);
    // ---
    foreach ($tat as $k => $tab) {
        // check if lang already in array array_key_exists
        $langcode = $tab['lang'];
        $target   = $tab['target'];
        // ---
        if (!array_key_exists($langcode, $vta)) {
            $vta[$langcode] = [];
        };
        // ---
        $views = isset($tab['views']) ? $tab['views'] : 0;
        // ---
        // add to array
        // ---
        $vta[$langcode][$target] = $views;
    }
    // ---
    return $vta;
}
//---
$settings_tab = get_td_or_sql_settings();
//---
foreach ($settings_tab as $Key => $table) {
    TablesSql::$s_settings[$table['title']] = $table;
}
