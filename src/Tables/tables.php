<?PHP

namespace Tables\Main;

//---
/*
(\$)(enwiki_pageviews_table|Words_table|All_Words_table|All_Refs_table|Lead_Refs_table|Assessments_table|Langs_table)\b

MainTables::$1x_$2

use Tables\Main\MainTables;

*/

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---

use function SQLorAPI\GetDataTab\get_td_or_sql_titles_infos;
use function Tables\TablesDir\open_td_Tables_file;

//---
class MainTables
{
	public static $x_enwiki_pageviews_table = [];
	public static $x_Words_table = [];
	public static $x_All_Words_table = [];
	public static $x_All_Refs_table = [];
	public static $x_Lead_Refs_table = [];
	public static $x_Assessments_table = [];
	public static $x_Langs_table = [];
}
// ---
$tables_d = array(
	'langs_tables' => &MainTables::$x_Langs_table,
);
//---
foreach ($tables_d as $key => &$value) {
	$value = open_td_Tables_file("jsons/{$key}.json");
}

$titles_infos = get_td_or_sql_titles_infos();

foreach ($titles_infos as $k => $tab) {
	$title = $tab['title'];
	// ---
	MainTables::$x_enwiki_pageviews_table[$title] = $tab['en_views'];
	// ---
	MainTables::$x_Words_table[$title] = $tab['w_lead_words'];
	MainTables::$x_All_Words_table[$title] = $tab['w_all_words'];
	// ---
	MainTables::$x_All_Refs_table[$title] = $tab['r_all_refs'];
	MainTables::$x_Lead_Refs_table[$title] = $tab['r_lead_refs'];
	// ---
	MainTables::$x_Assessments_table[$title] = $tab['importance'];
};
