<?PHP
//---
include_once __DIR__ . '/../actions/test_print.php';
include_once __DIR__ . '/../results/getcats.php';
include_once __DIR__ . '/../Tables/include.php';
include_once __DIR__ . '/../actions/load_request.php';
include_once __DIR__ . '/../actions/html.php';
include_once __DIR__ . '/../api_or_sql/index.php'; // namespace SQLorAPI\Get;

// include_once __DIR__ . '/include.php';

include_once __DIR__ . '/leader_filter.php';
include_once __DIR__ . '/subs/include.php';
include_once __DIR__ . '/graph.php';
include_once __DIR__ . '/lang_user_graph.php';

use function Leaderboard\Graph\print_graph_tab;
use function Leaderboard\Graph2\print_graph_tab_2_new;

echo <<<HTML
    <style>
    .border_debugx {
        border: 1px solid;
        border-radius: 5px;
    }
    </style>
HTML;

$get   = $_GET['get'] ?? '';
$users = $_GET['user'] ?? '';
$langs = $_GET['langcode'] ?? '';

if ($get == 'users' || !empty($users)) {
    include_once __DIR__ . '/users.php';
    // ---
} elseif ($get == 'langs' || !empty($langs)) {
    include_once __DIR__ . '/langs.php';
    // ---
} elseif (!empty($_GET['camps'] ?? '')) {
    // http://localhost:9001/Translation_Dashboard/leaderboard.php?camps=1&test=1
    // ---
    include_once __DIR__ . '/others/camps_text.php';
    // ---
} elseif (!empty($_GET['graph'] ?? '')) {
    // http://localhost:9001/Translation_Dashboard/leaderboard.php?graph=1&test=1
    // ---
    print_graph_tab();
    // ---
} elseif (!empty($_GET['graph_api'] ?? '')) {
    // http://localhost:9001/Translation_Dashboard/leaderboard.php?graph_api=1&test=1
    // ---
    include_once __DIR__ . '/others/graph_api.php'; // namespace Leaderboard\Graph2;
    print_graph_tab_2_new();
    // ---
} else {
    include_once __DIR__ . '/main.php';
}
