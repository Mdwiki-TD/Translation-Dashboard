<?php

namespace Results\ResultsHelps;

/*
Usage:

use function Results\ResultsHelps\print_r_it;
use function Results\ResultsHelps\get_lang_exists_pages;
use function Results\ResultsHelps\open_json_file;

*/

use function Actions\TestPrint\test_print;
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

function print_r_it($data, $title, $d = false, $r = false)
{
    $test11 = $_GET['test11'] ?? '';
    // ---
    if (empty($test11)) return;
    // ---
    echo "   -  $title: " . count($data) . "<br>";
    echo "<pre>";
    // ---
    if ($r !== false) {
        print_r($data);
    } elseif ($d !== false) {
        print(json_encode($data));
    }
    // ---
    echo "</pre>";
}

function get_lang_exists_pages($code)
{
    $json_file = "cash_exists/$code.json";
    $exists = open_td_Tables_file($json_file);

    return $exists;
}
