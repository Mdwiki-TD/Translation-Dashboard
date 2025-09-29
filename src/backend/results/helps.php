<?php

namespace Results\ResultsHelps;

/*
Usage:

use function Results\ResultsHelps\get_lang_exists_pages_from_cache;
use function Results\ResultsHelps\open_json_file;

*/

use function TD\Render\TestPrint\test_print;
use function Tables\TablesDir\open_td_Tables_file;

function open_json_file($file_path)
{
    if (!is_file($file_path)) {
        test_print("file $file_path does not exist");
        return [];
    }

    $text = file_get_contents($file_path);
    if ($text === false) {
        test_print("Failed to read file contents from $file_path");
        return [];
    }

    $data = json_decode($text, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        test_print("Failed to decode JSON from $file_path");
        return [];
    }

    return $data;
}

function get_lang_exists_pages_from_cache($code)
{
    // example of result like: [ "Spontaneous bacterial peritonitis", "Dronedarone", ... ]
    // ---
    $json_file = "cash_exists/$code.json";
    $exists = open_td_Tables_file($json_file);

    return $exists;
}
