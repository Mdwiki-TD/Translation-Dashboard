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
include_once __DIR__ . '/../api_or_sql/index.php';

use function SQLorAPI\GetDataTab\get_td_or_sql_titles_infos;

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
$tables_dir = __DIR__ . '/../../td/Tables';
//---
if (substr($tables_dir, 0, 2) == 'I:') {
	$tables_dir = 'I:/mdwiki/mdwiki/public_html/td/Tables';
}
//---
if (!getenv('tables_dir')) {
	// set env
	putenv('tables_dir=' . $tables_dir);
}
//---
// 'lang_code_to_en' => &$lang_code_to_en,
// 'medwiki_to_enwiki' => &$medwiki_to_enwiki,
//---
$tables_d = array(
	// 'enwiki_pageviews' => &MainTables::$x_enwiki_pageviews_table,
	// 'words' => &MainTables::$x_Words_table,
	// 'allwords' => &MainTables::$x_All_Words_table,
	// 'all_refcount' => &MainTables::$x_All_Refs_table,
	// 'lead_refcount' => &MainTables::$x_Lead_Refs_table,
	// 'assessments' => &MainTables::$x_Assessments_table,
	'langs_tables' => &MainTables::$x_Langs_table,
);
//---
foreach ($tables_d as $key => &$value) {
	$file = file_get_contents($tables_dir . "/jsons/{$key}.json");
	$value = json_decode($file, true);
}
//---
$titles_infos = get_td_or_sql_titles_infos();

// var_dump(json_encode($titles_infos, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
// [{ "title": "11p deletion syndrome", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 1592, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153" }, ...]
// ---
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
