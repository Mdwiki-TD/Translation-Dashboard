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
// ---
$get = filter_input(INPUT_GET, 'get', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

$langcode = filter_input(INPUT_GET, 'langcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
$mainlang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';

$mainuser = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
$year_y   = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';
$camp     = filter_input(INPUT_GET, 'camp', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';

//---
if ($get == 'users' || !empty($mainuser)) {
    // ---
    echo users_html($mainlang, $mainuser, $year_y, $camp);
    // ---
} elseif ($get == 'langs' || !empty($langcode)) {
    // ---
    echo langs_html($langcode, $year_y, $camp);
    // ---
} elseif (!empty($_GET['camps'] ?? '')) {
    // http://localhost:9001/Translation_Dashboard/leaderboard.php?camps=1&test=1
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
    echo print_graph_tab_2_new();
    // ---
} else {
    //---
    $user_group = filter_input(INPUT_GET, 'project', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ?? filter_input(INPUT_GET, 'user_group', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ?? 'all';
    //---
    echo main_leaderboard($year_y, $camp, $user_group);
}
