<?PHP

// foreach (glob(__DIR__ . "/*.php") as $filename) {
//     if ($filename == __FILE__) continue;
//     include_once $filename;
// }

include_once __DIR__ . '/frontend/include.php';
include_once __DIR__ . '/backend/include_first/include.php';

foreach (glob(__DIR__ . "/backend/api_calls/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/backend/td_api_wrap/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/backend/api_or_sql/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/backend/tables/*.php") as $filename) {
    if ($filename == __FILE__ || basename($filename) == 'langcode.php') {
        continue;
    }
    include_once $filename;
}

foreach (glob(__DIR__ . "/backend/others/*.php") as $filename) {
    include_once $filename;
}

include_once __DIR__ . '/backend/tables/langcode.php';
include_once __DIR__ . '/leaderboard/include_leaderboards.php';

include_once __DIR__ . '/results/include.php';
