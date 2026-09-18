<?PHP

include_once __DIR__ . '/leader_tables.php';
include_once __DIR__ . '/leader_tables_users.php';

include_once __DIR__ . '/leader_filter.php';

foreach (glob(__DIR__ . "/subs/*.php") as $filename) {
    include_once $filename;
}

include_once __DIR__ . '/graph.php';
include_once __DIR__ . '/lang_user_graph.php';

include_once __DIR__ . '/camps.php';

include_once __DIR__ . '/users.php';
include_once __DIR__ . '/langs.php';

// include_once __DIR__ . '/others/camps_text.php';
// include_once __DIR__ . '/others/graph_api.php'; // namespace Leaderboard\Graph2;
foreach (glob(__DIR__ . "/others/*.php") as $filename) {
    include_once $filename;
}
