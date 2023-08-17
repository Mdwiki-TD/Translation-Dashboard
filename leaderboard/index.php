<?PHP
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
require 'leaderboard/leader_tables.php';
//---
function print_cat_table(): string {
    global $sql_users_tab, $Articles_numbers, $Words_total, $sql_Languages_tab, $global_views;

    $numbersTable = createNumbersTable(
        count($sql_users_tab), 
        number_format($Articles_numbers), 
        number_format($Words_total), 
        count($sql_Languages_tab), 
        number_format($global_views)
    );
    $gg = print_graph();
    // $numbersCol = makeColSm4('Numbers', $numbersTable . "<br>" . $gg, 3);
    $numbersCol = makeColSm4('Numbers', $numbersTable, 3, $gg);

    $usersTable = makeUsersTable();
    $usersCol = makeColSm4('Top users by number of translation', $usersTable, 5);

    $languagesTable = makeLangTable();
    $languagesCol = makeColSm4('Top languages by number of Articles', $languagesTable, 4);
       
    return <<<HTML
        <br>
        <span align="center">
            <h3>Leaderboard</h3>
        </span>
        <div class="row">
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
$colg3 = 'col-md-3 col-sm-3';
$colg2 = 'col-md-2 col-sm-3';
// $col_lg_1 = 'col-md-1 col';
//---

$d33 = <<<HTML
<div class="$colg3">
    <div class="form-group">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">%s</span>
            </div>
            %s
        </div>
    </div>
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
$mYears = getMyYears();
$y3 = makeDropdown($mYears, $year, 'year', 'all');
$yearDropdown = sprintf($d33, 'Year', $y3);
//---
$uux = print_cat_table();
//---
$submitBtn = "<div class='aligncenter $colg2'><input class='btn btn-primary' type='submit' name='start' value='Filter' /></div>";
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
<form method="get" action="leaderboard.php">
    <div class="row">
        <div class="col-md-1 col-sm-0"></div>
        $campDropdown
        $projectDropdown
        $yearDropdown
        $submitBtn
    </div>
</form>

<div class="container-fluid">
    $uux
</div>
HTML;
//---
?>