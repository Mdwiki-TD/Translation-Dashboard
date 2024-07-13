<?PHP
//---

include_once __DIR__ . '/../results/getcats.php';
include_once __DIR__ . '/../Tables/tables.php';
include_once __DIR__ . '/../Tables/langcode.php';
include_once __DIR__ . '/../Tables/sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
include_once __DIR__ . '/../actions/functions.php';
include_once __DIR__ . '/../actions/html.php';

//---
include_once __DIR__ . '/camps.php';
include_once __DIR__ . '/filter_form.php';
include_once __DIR__ . '/leader_tables.php';
include_once __DIR__ . '/lead_help.php';
include_once __DIR__ . '/graph.php';
include_once __DIR__ . '/users_sub.php';
include_once __DIR__ . '/langs_sub.php';

use function Leaderboard\Graph\print_graph_tab;
use function Leaderboard\Camps\camps_list;

echo '<script>$("#leaderboard").addClass("active");</script>';

$users = $_REQUEST['user'] ?? '';
$langs = $_REQUEST['langcode'] ?? '';
$graph = $_REQUEST['graph'] ?? '';
$camps = $_REQUEST['camps'] ?? '';

if ($users !== '') {
    require __DIR__ . '/users.php';
} elseif ($langs !== '') {
    require __DIR__ . '/langs.php';
} elseif ($camps !== '') {
    require __DIR__ . '/camps.php';
    camps_list();
} elseif ($graph !== '') {
    print_graph_tab();
} else {
    require __DIR__ . '/main.php';
}
