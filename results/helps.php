<?php

namespace Results\ResultsHelps;

/*
Usage:

use function Results\ResultsHelps\print_r_it;
use function Results\ResultsHelps\get_lang_exists_pages;

*/

use function Results\GetCats\open_json_file;
use function Actions\TestPrint\test_print;


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
    // Determine the directory for JSON files
    $tables_dir = getenv('tables_dir') ?: __DIR__ . '/../../td/Tables';
    if (substr($tables_dir, 0, 2) === 'I:') {
        $tables_dir = 'I:/mdwiki/mdwiki/public_html/td/Tables';
    }
    // Load existing pages from JSON file
    $json_file = "$tables_dir/cash_exists/$code.json";
    $exists = open_json_file($json_file);
    test_print("$json_file: Exists size: " . count($exists));
    return $exists;
}
