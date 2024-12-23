<?PHP
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
$Assessments_fff = array(
	'Top' => 1,
	'High' => 2,
	'Mid' => 3,
	'Low' => 4,
	'Unknown' => 5,
	'' => 5
);
//---
$tables = array(
	'enwiki_pageviews' => &$enwiki_pageviews_table,
	// 'lang_code_to_en' => &$lang_code_to_en,
	'langs_tables' => &$Langs_table,
	'words' => &$Words_table,
	'allwords' => &$All_Words_table,
	'all_refcount' => &$All_Refs_table,
	'lead_refcount' => &$Lead_Refs_table,
	// 'medwiki_to_enwiki' => &$medwiki_to_enwiki,
	'assessments' => &$Assessments_table
);
//---
if (!getenv('tables_dir')) {
	// set env
	$tables_dir = __DIR__ . '/../../td/Tables';

	if (substr($tables_dir, 0, 2) == 'I:') {
		$tables_dir = 'I:/mdwiki/mdwiki/public_html/td/Tables';
	}
	putenv('tables_dir=' . $tables_dir);
}
//---
foreach ($tables as $key => &$value) {
	$file = file_get_contents(getenv('tables_dir') . "/jsons/{$key}.json");
	$value = json_decode($file, true);
}
//---
