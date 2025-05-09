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
include_once __DIR__ . '/leader_tables.php'; // namespace Leaderboard\LeaderTables;
include_once __DIR__ . '/leader_tables_tabs.php';
include_once __DIR__ . '/leader_tables_users.php';
include_once __DIR__ . '/graph.php';
include_once __DIR__ . '/graph_api.php';
//---
use Leaderboard\Tabs\LeaderBoardTabs;
use Tables\SqlTables\TablesSql;
use function Actions\Html\makeDropdown;
use function Actions\Html\makeColSm4;
use function Leaderboard\Graph\print_graph_for_table;
use function Leaderboard\LeaderTables\createNumbersTable;
use function Leaderboard\LeaderTables\makeLangTable;
use function Leaderboard\LeaderTabUsers\makeUsersTable;
use function Leaderboard\LeaderTabUsers\module_copy;
use function SQLorAPI\Get\get_pages_with_pupdate;
use function SQLorAPI\GetDataTab\get_td_or_sql_projects;

function print_cat_table($year, $user_group, $cat): string
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
//---
$year       = $_GET['year'] ?? 'all';
$camp       = $_GET['camp'] ?? 'all';
$project    = $_GET['project'] ?? 'all';
//---
$d33 = <<<HTML
<div class="input-group">
    <span class="input-group-text">%s</span>
    %s
</div>
HTML;
//---
$y1 = makeDropdown(TablesSql::$s_cat_titles, $camp, 'camp', 'all');
$campDropdown = sprintf($d33, 'Campaign', $y1);
//---
$projects_title_to_id = [];
//---
$projects_tab = get_td_or_sql_projects();
//---
foreach ($projects_tab as $Key => $table) $projects_title_to_id[$table['g_title']] = $table['g_id'];
//---
$projects = array_keys($projects_title_to_id);
//---
$y2 = makeDropdown($projects, $project, 'project', 'all');
$projectDropdown = sprintf($d33, 'Translators', $y2);
//---
$m_years2 = get_pages_with_pupdate();
//---
// sort $m_years2 from biggest to smallest
rsort($m_years2);
//---
$y3 = makeDropdown($m_years2, $year, 'year', 'all');
$yearDropdown = sprintf($d33, 'Year', $y3);
//---
$cat = TablesSql::$s_camp_to_cat[$camp] ?? '';
//---
$uux = print_cat_table($year, $project, $cat);
//---
echo <<<HTML
<style>
    .table>tbody>tr>td,
    .table>tbody>tr>th,
    .table>thead>tr>td,
    .table>thead>tr>th {
        padding: 6px;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 1px solid #ddd;
    }
</style>
HTML;
//---
$test_line = (isset($_REQUEST['test']) != '') ? "<input type='text' name='test' value='1' hidden/>" : "";
//---
echo <<<HTML
<form method="get" action="leaderboard.php">
    <div class="row g-3">
        <div class="col-md-3">
            <span align="center">
                <h3>Leaderboard</h3>
            </span>
        </div>
        <div class="col-md-7">
            <div class="row">
                <div class="col-md-5">
                    $campDropdown
                </div>
                <div class="col-md-4">
                    $projectDropdown
                </div>
                <div class="col-md-3">
                    $yearDropdown
                </div>
            </div>
        </div>
        <div class="aligncenter col-md-1 col-sm-3">
            $test_line
            <input class='btn btn-outline-primary' type='submit' name='start' value='Filter' />
        </div>
    </div>
</form>
<hr/>
<div class="container-fluid">
    $uux
</div>
HTML;
//---
