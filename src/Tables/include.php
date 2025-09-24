<?PHP
/*
include_once __DIR__ . '/tables.php';
include_once __DIR__ . '/langcode.php';
include_once __DIR__ . '/sql_tables.php';
*/

foreach (glob(__DIR__ . "/*.php") as $filename) {
    if ($filename == __FILE__ || basename($filename) == 'langcode.php') {
        continue;
    }
    include_once $filename;
}

include_once __DIR__ . '/langcode.php';
