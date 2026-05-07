<?PHP

namespace Tables\SqlTables;

use function SQLorAPI\GetDataTab\get_td_or_sql_categories;
use function SQLorAPI\GetDataTab\get_td_or_sql_settings;

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

$settings_tab = get_td_or_sql_settings();

foreach ($settings_tab as $Key => $table) {
    TablesSql::$s_settings[$table['title']] = $table;
}
