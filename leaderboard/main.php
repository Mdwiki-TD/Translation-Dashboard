<?PHP

namespace Leaderboard\Index;

/*
Usage:

use function Leaderboard\Index\print_cat_table;

*/

//---
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
include_once __DIR__ . '/include.php';
//---
use Leaderboard\Tabs\LeaderBoardTabs;
use Tables\SqlTables\TablesSql;
use function Actions\Html\makeColSm4;
use function Leaderboard\Graph\print_graph_for_table;
use function Leaderboard\LeaderTables\createNumbersTable;
use function Leaderboard\LeaderTables\makeLangTable;
use function Leaderboard\LeaderTabUsers\makeUsersTable;
use function Leaderboard\LeaderTabUsers\module_copy;
use function Leaderboard\Filter\leaderboard_filter;

function print_cat_table($year, $user_group, $camp, $cat): string
{
    $numbersTable = createNumbersTable(
        count(LeaderBoardTabs::$u_sql_users_tab),
        number_format(LeaderBoardTabs::$u_Articles_numbers),
        number_format(LeaderBoardTabs::$u_Words_total),
        count(LeaderBoardTabs::$u_sql_Languages_tab),
        number_format(LeaderBoardTabs::$u_global_views)
    );
    //---
    // $gg = print_graph_api(LeaderBoardTabs::$u_tab_for_graph2, $id = "chart09", $no_card = false);
    $gg = print_graph_for_table(LeaderBoardTabs::$u_tab_for_graph, $id = 'chart09', $no_card = false);
    //---

    $numbersCol = makeColSm4('Numbers', $numbersTable, 3, $gg);

    $usersTable = makeUsersTable();

    $copy_module = module_copy($year, $user_group, $cat);

    $modal_a = <<<HTML
        <button type="button" class="btn-tool" href="#" data-bs-toggle="modal" data-bs-target="#targets">
            <i class="fas fa-copy"></i>
        </button>
    HTML;
    //---
    $usersCol = makeColSm4('Top users by number of translation', $usersTable, 5, $table2 = $copy_module, $title2 = $modal_a);
    //---
    $languagesTable = makeLangTable();
    $languagesCol = makeColSm4('Top languages by number of Articles', $languagesTable, 4);

    return <<<HTML
        <div class="row g-3">
            $numbersCol
            $usersCol
            $languagesCol
        </div>
    HTML;
}

$year       = $_GET['year'] ?? 'all';
$camp       = $_GET['camp'] ?? 'all';
$user_group    = $_GET['project'] ?? $_GET['user_group'] ?? 'all';
//---
$filter_form = leaderboard_filter($year, $user_group, $camp);
//---
$cat = TablesSql::$s_camp_to_cat[$camp] ?? '';
//---
$uux = print_cat_table($year, $user_group, $camp, $cat);
//---
echo <<<HTML
    $filter_form
    <hr/>
    <div class="container-fluid">
        $uux
    </div>
HTML;
//---
