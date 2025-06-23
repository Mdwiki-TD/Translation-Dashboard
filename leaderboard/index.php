<?PHP
//---
include_once __DIR__ . '/include.php';
include_once __DIR__ . '/main.php';

use function Leaderboard\Graph\print_graph_tab;
use function Leaderboard\Graph2\print_graph_tab_2_new;
use function Leaderboard\Index\main_leaderboard;
use function Leaderboard\CampText\echo_html;
use function Leaderboard\Langs\langs_html;
use function Leaderboard\Users\users_html;

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
    echo users_html();
    // ---
} elseif ($get == 'langs' || !empty($langs)) {
    include_once __DIR__ . '/langs.php';
    // ---
    echo langs_html();
    // ---
} elseif (!empty($_GET['camps'] ?? '')) {
    // http://localhost:9001/Translation_Dashboard/leaderboard.php?camps=1&test=1
    // ---
    include_once __DIR__ . '/others/camps_text.php';
    // ---
    echo echo_html();
    // ---
} elseif (!empty($_GET['graph'] ?? '')) {
    // http://localhost:9001/Translation_Dashboard/leaderboard.php?graph=1&test=1
    // ---
    echo print_graph_tab();
    // ---
} elseif (!empty($_GET['graph_api'] ?? '')) {
    // http://localhost:9001/Translation_Dashboard/leaderboard.php?graph_api=1&test=1
    // ---
    include_once __DIR__ . '/others/graph_api.php'; // namespace Leaderboard\Graph2;
    echo print_graph_tab_2_new();
    // ---
} else {
    echo main_leaderboard();
}
