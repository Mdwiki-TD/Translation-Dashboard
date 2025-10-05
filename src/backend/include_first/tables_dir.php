<?PHP

namespace Tables\TablesDir;
/*

use function Tables\TablesDir\open_td_Tables_file;

*/

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};

use function TD\Render\TestPrint\test_print;

function open_td_Tables_file($path, $echo = true)
{
	// $tables_dir = getenv("HOME") . '/public_html/td/Tables';
	// if (substr(__DIR__, 0, 2) == 'I:') $tables_dir = 'I:/mdwiki/mdwiki/public_html/td/Tables';
	//---
	$home_dir = getenv("HOME") ?: 'I:/mdwiki/mdwiki';
	$tables_dir = $home_dir . '/public_html/td/Tables';
	//---
	$file_path = "$tables_dir/$path";
	//---
	if (!is_file($file_path)) {
		test_print("---- open_td_Tables_file: file $file_path does not exist");
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
		test_print("---- open_td_Tables_file File: $file_path: Exists size: $len");
	}

	return $result;
}
