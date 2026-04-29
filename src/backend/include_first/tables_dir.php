<?PHP

namespace Tables\TablesDir;
/*

use function Tables\TablesDir\open_td_tables_file;

*/

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};

use OAuth\Settings\Settings;
use function TD\Render\TestPrint\test_print;

function open_td_tables_file($path, $echo = true)
{
	//---
	// $home_dir = getenv("HOME") ?: 'I:/MD_TOOLS/MDWIKI_MAIN_REPO';
	// $json_tables_path = $home_dir . '/public_html/td/Tables';
	//---
	$settings = Settings::getInstance();
	$json_tables_path = $settings->TablesPath;
	//---
	$file_path = "$json_tables_path/jsons/$path";
	//---
	if (!is_file($file_path)) {
		test_print("---- open_td_tables_file: file $file_path does not exist");
		return [];
	}
	$contents = file_get_contents($file_path);

	if ($contents === false) {
		test_print("---- Failed to read file contents from $file_path");
		return [];
	}

	$result = json_decode($contents, true);

	if ($result === null || $result === false) {
		test_print("---- Failed to decode JSON from $file_path");
		$result = [];
	} elseif ($echo) {
		$len = count($result);
		if (isset($result['list'])) $len = count($result['list']);
		// ---
		test_print("---- open_td_tables_file File: $file_path: Exists size: $len");
	}

	return $result;
}
