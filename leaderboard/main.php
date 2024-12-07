<?PHP

namespace Leaderboard\Index;

/*
Usage:

use function Leaderboard\Index\print_cat_table;

*/

//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
include_once __DIR__ . '/graph.php';
include_once __DIR__ . '/graph_api.php';
//---
use function Actions\Html\makeDropdown;
use function Actions\Html\makeColSm4;
use function Leaderboard\Graph\print_graph_for_table;
use function Leaderboard\Graph2\print_graph_api;
use function Leaderboard\LeaderTables\createNumbersTable;
use function Leaderboard\LeaderTables\makeLangTable;
use function Leaderboard\LeaderTabUsers\makeUsersTable;
use function Leaderboard\LeaderTabUsers\module_copy;
use function SQLorAPI\Get\get_pages_with_pupdate;

function print_cat_table(): string
{
    global $sql_users_tab, $Articles_numbers, $Words_total, $sql_Languages_tab, $global_views, $tab_for_graph, $tab_for_graph2;

    $numbersTable = createNumbersTable(
        count($sql_users_tab),
        number_format($Articles_numbers),
        number_format($Words_total),
        count($sql_Languages_tab),
        number_format($global_views)
    );
    //---
    // $gg = print_graph_api($tab_for_graph2, $id = "chart09", $no_card = false);
    $gg = print_graph_for_table($tab_for_graph, $id = 'chart09', $no_card = false);
    //---

    $numbersCol = makeColSm4('Numbers', $numbersTable, 3, $gg);

    $usersTable = makeUsersTable();
    $copy_module = module_copy();

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
$year       = $_REQUEST['year'] ?? 'all';
$camp       = $_REQUEST['camp'] ?? 'all';
$project    = $_REQUEST['project'] ?? 'all';
//---
$d33 = <<<HTML
<div class="input-group">
    <span class="input-group-text">%s</span>
    %s
</div>
HTML;
//---
$y1 = makeDropdown($cat_titles, $camp, 'camp', 'all');
$campDropdown = sprintf($d33, 'Campaign', $y1);
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
$uux = print_cat_table();
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
