<?PHP
//---
require('header.php');
require('leader_tables.php');
//---
echo '
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
    </style>';
//---
$year      = isset($_REQUEST['year']) ? $_REQUEST['year']   : 'all';
$camp      = isset($_REQUEST['camp']) ? $_REQUEST['camp'] : 'all';
$project   = isset($_REQUEST['project']) ? $_REQUEST['project'] : 'all';
//---
function make_drop_d($tab, $cat, $id) {
    //---
    $lines = "";
    //---
    foreach ( $tab AS $dd ) {
        //---
        $se = '';
        //---
        if ( $cat == $dd ) $se = 'selected';
        //---
        $lines .= "
	    <option value='$dd' $se>$dd</option>
		";
        //---
    };
    //---
	$sel = "";
	//---
	if ( $cat == 'all' ) $sel = "celected";
	//---
    $texte = "
        <select dir='ltr' id='$id' name='$id' class='form-select'>
            <option value='all' $sel>all</option>
			$lines
        </select>";
    //---
    return $texte;
    //---
};
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
$y1 = make_drop_d($cat_titles, $camp, 'camp');
$d1 = sprintf($d33, 'Campaign', $y1);
//---
$projects = array();
//---
foreach ( quary2('select g_title from projects;') AS $Key => $table ) $projects[] = $table['g_title'];
$y2 = make_drop_d($projects, $project, 'project');
$d2 = sprintf($d33, 'Translators', $y2);
//---
$my_years = get_my_years();
$y3 = make_drop_d($my_years, $year, 'year');
$d3 = sprintf($d33, 'Year', $y3);
//---
echo "
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
function print_cat_table() {
    //---
    $tab1 = create_Numbers_table();
    $div1 = make_col_sm_4('Numbers',$tab1, $numb = '3');
    //---
    $tab2 = Make_users_table();
    $div2 = make_col_sm_4('Top users by number of translation', $tab2, $numb = '5');
    //---
    $tab3 = Make_lang_table();
    $div3 = make_col_sm_4('Top languages by number of Articles',$tab3);
    //---
    $dad1 = "
    <div class='card'>
        <div class='card-header aligncenter'>
            <div class='card-title' style='margin-bottom: 0rem;' >
                <h3>Leaderboard</h3>
            </div>
        </div>
        <div class='card-body'>
            <div class='row'>
                $div1
                $div2
                $div3
            </div>
        </div>
    </div>
    ";
    //---
    $dad = "
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
    ";
    //---
    return $dad;
};
//---
echo "\n<div class='container-fluid'>\n";
echo print_cat_table();
//---
echo "</div>
<!--</section>-->
"; 
//---
require('foter.php');
//---

//---
?>