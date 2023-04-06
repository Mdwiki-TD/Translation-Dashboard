<?PHP
//---
require('leader_tables.php');
//---
function print_cat_table(): string
{
    global $sql_users_tab, $Articles_numbers, $Words_total, $sql_Languages_tab, $global_views;

    $numbersTable = createNumbersTable(count($sql_users_tab), number_format($Articles_numbers), number_format($Words_total), count($sql_Languages_tab), number_format($global_views));
    $numbersCol = makeColSm4('Numbers', $numbersTable, $numb = '3');

    $usersTable = makeUsersTable();
    $usersCol = makeColSm4('Top users by number of translation', $usersTable, $numb = '5');

    $languagesTable = makeLangTable();
    $languagesCol = makeColSm4('Top languages by number of Articles', $languagesTable, $numb = '4');
    return <<<HTML
        <br>
        <span align=center>
            <h3>Leaderboard</h3>
        </span>
        <div class='row'>
            $numbersCol
            $usersCol
            $languagesCol
        </div>
    HTML;
}
//---
function print_cat_table_1() {
    //---
    global $sql_users_tab,$Articles_numbers,$Words_total,$sql_Languages_tab,$global_views;
    //---
    // $tab1 = create_Numbers_table();
    $tab1 = createNumbersTable(count($sql_users_tab), number_format($Articles_numbers), number_format($Words_total), count($sql_Languages_tab), number_format($global_views));
	//---
    $div1 = makeColSm4('Numbers',$tab1, $numb = '3');
    //---
    $tab2 = makeUsersTable();
    $div2 = makeColSm4('Top users by number of translation', $tab2, $numb = '5');
    //---
    $tab3 = makeLangTable();
    $div3 = makeColSm4('Top languages by number of Articles',$tab3, $numb = '4');
    //---
    return <<<HTML
    <br>
        <span align=center>
            <h3>Leaderboard</h3>
        </span>
        <div class='row'>
            $div1
            $div2
            $div3
        </div>
    </div>
    HTML;
};
//---
$year      = isset($_REQUEST['year']) ? $_REQUEST['year']   : 'all';
$camp      = isset($_REQUEST['camp']) ? $_REQUEST['camp'] : 'all';
$project   = isset($_REQUEST['project']) ? $_REQUEST['project'] : 'all';
//---
$colg3 = 'col-md-3 col-sm-3';
$colg2 = 'col-md-2 col-sm-3';
// $col_lg_1 = 'col-md-1 col';
//---
$d33 = "
<div class='$colg3'>
    <div class='form-group'>
        <div class='input-group'>
            <div class='input-group-prepend'>
                <span class='input-group-text'>%s</span>
            </div>
                    %s
        </div>
    </div>
</div>";
//---
$y1 = make_drop_d($cat_titles, $camp, 'camp', 'all');
$d1 = sprintf($d33, 'Campaign', $y1);
//---
$projects = array();
//---
foreach ( execute_query('select g_title from projects;') AS $Key => $table ) $projects[] = $table['g_title'];
$y2 = make_drop_d($projects, $project, 'project', 'all');
$d2 = sprintf($d33, 'Translators', $y2);
//---
$m_years = get_my_years();
$y3 = make_drop_d($m_years, $year, 'year', 'all');
$d3 = sprintf($d33, 'Year', $y3);
//---
$uux = print_cat_table();
//---
echo "
<style>
    .table>tbody>tr>td,
    .table>tbody>tr>th,
    .table>thead>tr>td,
    .table>thead>tr>th {
        padding: 6px;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 1px solid #ddd
    }
</style>
<form method='get' action='leaderboard.php'>
<div class='row'>
    <div class='col-md-1 col-sm-0'></div>
    $d1
    $d2
    $d3
    <div class='aligncenter $colg2'><input class='btn btn-primary' type='submit' name='start' value='Filter' /></div>
</div>
</form>";
//---
echo "
<div class='container-fluid'>
$uux
</div>
";
//---
?>