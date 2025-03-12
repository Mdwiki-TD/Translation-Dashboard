<?PHP
//---

include_once __DIR__ . '/../actions/test_print.php';
include_once __DIR__ . '/../results/getcats.php';
include_once __DIR__ . '/../Tables/tables.php';
include_once __DIR__ . '/../Tables/langcode.php';
include_once __DIR__ . '/../Tables/sql_tables.php'; // $cat_titles $cat_to_camp $camp_to_cat
include_once __DIR__ . '/../actions/functions.php';
include_once __DIR__ . '/../actions/html.php';

//---
include_once __DIR__ . '/subs/filter_form.php';
include_once __DIR__ . '/../api_or_sql/index.php';
include_once __DIR__ . '/subs/users_sub.php';
include_once __DIR__ . '/subs/langs_sub.php';
include_once __DIR__ . '/subs/lead_help.php';

include_once __DIR__ . '/camps.php';


include_once __DIR__ . '/leader_tables.php';
include_once __DIR__ . '/leader_tables_users.php';

include_once __DIR__ . '/graph.php';
include_once __DIR__ . '/graph_api.php';

use function Leaderboard\Graph2\print_graph_tab_2_new;
use function Leaderboard\Graph\print_graph_tab;
use function Leaderboard\Camps\camps_list;

echo '<script>$("#leaderboard").addClass("active");</script>';

$users = $_REQUEST['user'] ?? '';
$langs = $_REQUEST['langcode'] ?? '';
$graph = $_REQUEST['graph'] ?? '';
$graph_api = $_REQUEST['graph_api'] ?? '';
$camps = $_REQUEST['camps'] ?? '';

if (!empty($users)) {
    include_once __DIR__ . '/users.php';
} elseif (!empty($langs)) {
    include_once __DIR__ . '/langs.php';
} elseif (!empty($camps)) {
    include_once __DIR__ . '/camps.php';
    camps_list();
} elseif (!empty($graph)) {
    print_graph_tab();
} elseif (!empty($graph_api)) {
    print_graph_tab_2_new();
} else {
    include_once __DIR__ . '/main.php';
}
