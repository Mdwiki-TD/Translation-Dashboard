<?PHP

namespace Tables\SqlTables;

use function SQLorAPI\GetDataTab\get_td_or_sql_categories;
use function SQLorAPI\GetDataTab\get_td_or_sql_settings;
use function SQLorAPI\GetDataTab\get_td_or_sql_views;

class TablesSql
{
    public static $s_cat_to_camp = [];


    public static $s_camps_cat2 = [];
    public static $s_camp_input_depth = [];

    public static $s_catinput_list = [];
    public static $s_settings = [];
}



$categories_tab = get_td_or_sql_categories();

foreach ($categories_tab as $k => $tab) {
    if (!empty($tab['category']) && !empty($tab['campaign'])) {
        TablesSql::$s_camps_cat2[$tab['campaign']] = $tab['category2'];
        TablesSql::$s_cat_to_camp[$tab['category']] = $tab['campaign'];
        TablesSql::$s_catinput_list[$tab['category']] = $tab['category'];
        TablesSql::$s_camp_input_depth[$tab['campaign']] = $tab['depth'];
    };
};

function make_views_by_lang_target($year, $lang)
{
    $vta = [];

    $tat = get_td_or_sql_views($year, $lang);

    foreach ($tat as $k => $tab) {
        // check if lang already in array array_key_exists
        $langcode = $tab['lang'];
        $target   = $tab['target'];

        if (!array_key_exists($langcode, $vta)) {
            $vta[$langcode] = [];
        };

        $views = isset($tab['views']) ? $tab['views'] : 0;

        // add to array

        $vta[$langcode][$target] = $views;
    }

    return $vta;
}

$settings_tab = get_td_or_sql_settings();

foreach ($settings_tab as $Key => $table) {
    TablesSql::$s_settings[$table['title']] = $table;
}
