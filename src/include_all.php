<?PHP

// foreach (glob(__DIR__ . "/*.php") as $filename) {
//     if ($filename == __FILE__) continue;
//     include_once $filename;
// }

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

include_once __DIR__ . "/Tables/include.php";

include_once __DIR__ . '/leaderboard/include_leaderboards.php';

include_once __DIR__ . '/results/include.php';
