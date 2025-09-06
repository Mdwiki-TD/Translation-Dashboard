<?PHP

// include_once __DIR__ . '/include.php';

include_once __DIR__ . '/../actions/test_print.php';
include_once __DIR__ . '/../results/getcats.php';
include_once __DIR__ . '/../Tables/include.php';
include_once __DIR__ . '/../actions/load_request.php';
include_once __DIR__ . '/../actions/html.php';
include_once __DIR__ . '/../api_or_sql/index.php'; // namespace SQLorAPI\Get;

include_once __DIR__ . '/leader_tables.php';
include_once __DIR__ . '/leader_tables_users.php';

include_once __DIR__ . '/leader_filter.php';

// include_once __DIR__ . '/subs/include.php';
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
