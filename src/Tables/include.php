<?PHP
/*
include_once __DIR__ . '/tables.php';
include_once __DIR__ . '/langcode.php';
include_once __DIR__ . '/sql_tables.php';
*/

include_once __DIR__ . '/tables_dir.php';

foreach (glob(__DIR__ . "/*.php") as $filename) {
    if ($filename == __FILE__) {
        continue;
    }
    include_once $filename;
}
