<?php

namespace App\Utils\TablesDir;

use App\Logger;

function open_td_tables_file($filePath)
{

	if (!is_file($filePath)) {
		Logger::debug("---- open_td_tables_file: file $filePath does not exist");
		return [];
	}
	$contents = file_get_contents($filePath);

	if ($contents === false) {
		Logger::debug("---- Failed to read file contents from $filePath");
		return [];
	}

	$result = json_decode($contents, true);

	if ($result === null || $result === false) {
		Logger::debug("---- Failed to decode JSON from $filePath");
		return [];
	}

	$len = count($result);
	if (isset($result['list'])) $len = count($result['list']);
	Logger::debug("---- open_td_tables_file File: $filePath: Exists size: $len");

	return $result;
}
