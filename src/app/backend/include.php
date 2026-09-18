<?PHP

include_once __DIR__ . '/settings.php';
include_once __DIR__ . '/include_first/include.php';

foreach (glob(__DIR__ . "/api_calls/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/td_api_wrap/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/api_or_sql/*.php") as $filename) {
    include_once $filename;
}

include_once __DIR__ . '/tables/langcode.php';

foreach (glob(__DIR__ . "/others/*.php") as $filename) {
    include_once $filename;
}

include_once __DIR__ . "/results_27/include.php";

