<?PHP

namespace App\Tables\TablesDir;

use App\Logger;

function open_td_tables_file($file_path, $echo = true)
{

	if (!is_file($file_path)) {
		Logger::debug("---- open_td_tables_file: file $file_path does not exist");
		return [];
	}
	$contents = file_get_contents($file_path);

	if ($contents === false) {
		Logger::debug("---- Failed to read file contents from $file_path");
		return [];
	}

	$result = json_decode($contents, true);

	if ($result === null || $result === false) {
		Logger::debug("---- Failed to decode JSON from $file_path");
		$result = [];
	} elseif ($echo) {
		$len = count($result);
		if (isset($result['list'])) $len = count($result['list']);

		Logger::debug("---- open_td_tables_file File: $file_path: Exists size: $len");
	}

	return $result;
}
