<?PHP
//---

include_once __DIR__ . '/../actions/test_print.php';
include_once __DIR__ . '/../results/getcats.php';
include_once __DIR__ . '/../Tables/include.php';
include_once __DIR__ . '/../actions/load_request.php';
include_once __DIR__ . '/../actions/html.php';

//---
include_once __DIR__ . '/leader_filter.php';
include_once __DIR__ . '/subs/include.php';
include_once __DIR__ . '/../api_or_sql/index.php'; // namespace SQLorAPI\Get;

// include_once __DIR__ . '/camps.php'; // namespace Leaderboard\Camps;


include_once __DIR__ . '/graph.php'; // namespace Leaderboard\Graph;
include_once __DIR__ . '/lang_user_graph.php'; // namespace Leaderboard\Graph;

use function Leaderboard\Graph\print_graph_tab;
use function Leaderboard\Graph2\print_graph_tab_2_new;

echo <<<HTML
    <style>
    .border_debugx {
        border: 1px solid;
        border-radius: 5px;
    }
    </style>
    <script>$("#leaderboard").addClass("active");</script>
HTML;

$users = $_GET['user'] ?? '';
$langs = $_GET['langcode'] ?? '';
$graph = $_GET['graph'] ?? '';
$camps = $_GET['camps'] ?? '';

if (!empty($users)) {
    include_once __DIR__ . '/users.php';
    // ---
} elseif (!empty($langs)) {
    include_once __DIR__ . '/langs.php';
    // ---
} elseif (!empty($camps)) {
    include_once __DIR__ . '/camps_text.php';
    // ---
} elseif (!empty($graph)) {
    print_graph_tab();
    // ---
} elseif (!empty($_GET['graph_api'] ?? '')) {
    include_once __DIR__ . '/graph_api.php'; // namespace Leaderboard\Graph2;
    print_graph_tab_2_new();
    // ---
} else {
    include_once __DIR__ . '/main.php';
}
