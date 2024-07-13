<?PHP
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
include_once 'leaderboard/leader_tables.php';
//---
function print_cat_table(): string {
    global $sql_users_tab, $Articles_numbers, $Words_total, $sql_Languages_tab, $global_views, $tab_for_graph;

    $numbersTable = createNumbersTable(
        count($sql_users_tab),
        number_format($Articles_numbers),
        number_format($Words_total),
        count($sql_Languages_tab),
        number_format($global_views)
    );
    //---
    // $gg = print_graph_from_sql();
    $gg = print_graph_for_table($tab_for_graph, $id='chart09', $no_card=false);
    //---

    $numbersCol = makeColSm4('Numbers', $numbersTable, 3, $gg);

    $usersTable = makeUsersTable();

    $modal_a = <<<HTML
        <button type="button" class="btn btn-tool" href="#" data-bs-toggle="modal" data-bs-target="#targets">
            <i class="fas fa-copy"></i>
        </button>
    HTML;
    //---
    $usersCol = makeColSm4('Top users by number of translation', $usersTable, 5, $table2='', $title2=$modal_a);
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
$mYears = array_map('current', execute_query("SELECT DISTINCT LEFT(pupdate, 4) AS year FROM pages WHERE pupdate <> ''"));
//---
$y3 = makeDropdown($mYears, $year, 'year', 'all');
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
$colg3 = 'col-md-3 col-sm-3';
$colg2 = 'col-md-2 col-sm-3';
$colg1 = 'col-md-1 col-sm-3';
//---
echo <<<HTML
<form method="get" action="leaderboard.php">
    <div class="row g-3">
        <div class="col-md-3">
            <span align="center">
                <h3>Leaderboard</h3>
            </span>
        </div>
        <div class="$colg2">
            $campDropdown
        </div>
        <div class="$colg2">
            $projectDropdown
        </div>
        <div class="$colg2">
            $yearDropdown
        </div>
        <div class="aligncenter $colg1">
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
