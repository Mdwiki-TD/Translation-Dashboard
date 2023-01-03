<?PHP
//---
require('header.php');
require('tables.php');
include_once('functions.php');
require('tables_calendar.php');
//---
//---
echo '
    <style>

    .table {
        width: 95%;
    }
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
';
//---
echo years_start('calendar.php');
echo months_start();
//---
$Month_Page_views = 0;
$Views_for_month_by_users = array();
$Views_by_lang_for_month = array();
//---
$get_month = $_REQUEST['month'];
$last_month = date("Y-m");
$month_to_work = isset($get_month) ? $get_month : $last_month;   #2021-12
//---
function make_calendar_arrays($month_to_work) {
    //---
    global $Month_Page_views, $Views_for_month_by_users, $Views_by_lang_for_month;
    //---
    $table_name = '';                           # views_by_month_22
    $month_col  = '';                           # v_2022_02
    //---
    $y = '';
    $m = '';
    //---
    if (preg_match_all('/20([0-9]{2})-([0-9]{2})/', $month_to_work, $matches)) {
        $y = $matches[1][0];
        $m = $matches[2][0];
        $table_name = 'views_by_month_' . $y;
        $month_col  = 'v_20' . $y . '_' . $m;   #v_2022_02
    };
    //---
    $date_n = "20$y-$m-%";
    //---
    $sqlqua = "select p.target, p.user, v.lang, 
    v." . $month_col . "
    from pages p, " . $table_name . " v
    where p.lang = v.lang
    and p.target = v.target
    and p.pupdate like '" . $date_n . "'
    ;
    ";
    //---
    // echo '<h2>' . $sqlqua . '</h2>';
    //---
    $sql_cu = quary2($sqlqua);
    //---
    if ($_REQUEST['test'] != '' ) echo $sqlqua;
    //---
    //---
    foreach ($sql_cu as $id => $row) {
        $target = $row['target'];
        $views = isset($row[$month_col]) ? $row[$month_col] : 0;
        //---
        $Month_Page_views += $views;
        //---
        $user = $row['user'];
        if (isset($Views_for_month_by_users[$user]) == '') { $Views_for_month_by_users[$user] = 0; };
        //---
        $Views_for_month_by_users[$user] = $Views_for_month_by_users[$user] + $views;
        //---
        $lang = $row['lang'];
        if (isset($Views_by_lang_for_month[$lang]) == '') { $Views_by_lang_for_month[$lang] = 0; };
        //---
        $Views_by_lang_for_month[$lang] = $Views_by_lang_for_month[$lang] + $views;
    };
    //---
    /*CREATE TABLE views_by_month_22 (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        lang VARCHAR(30) NOT NULL,
        target VARCHAR(120) NOT NULL,
        v_2022_01 INT(6) NULL,
        v_2022_02 INT(6) NULL,
        v_2022_03 INT(6) NULL,
        v_2022_04 INT(6) NULL,
        v_2022_05 INT(6) NULL,
        v_2022_06 INT(6) NULL,
        v_2022_07 INT(6) NULL,
        v_2022_08 INT(6) NULL,
        v_2022_09 INT(6) NULL,
        v_2022_10 INT(6) NULL,
        v_2022_11 INT(6) NULL,
        v_2022_12 INT(6) NULL
        )
    */
    //---
};
//---
make_calendar_arrays($month_to_work);
//---
echo '<section>
<div class="container-fluid">';
//---
$month_main = '';
//---
$tab_m1 = Make_Numbers_table(
    count($Count_users_cal[$month_to_work]), 
    number_format($Count_articles_cal[$month_to_work]), 
    number_format($Total_words_cal[$month_to_work]), 
    count($Count_langs_cal[$month_to_work]),
    $Month_Page_views
);
$month_main .= make_col_sm_4('Numbers',$tab_m1, $numb = '3');
//---
$tab_m2 = Make_users_table_cal($Views_for_month_by_users,$Count_users_cal[$month_to_work],$Users_words_cal[$month_to_work]);

$month_main .= make_col_sm_4('Top users by number of translation', $tab_m2, $numb = '5');
//---
$tab_m3 = Make_lang_table_cal($Count_langs_cal[$month_to_work],$Views_by_lang_for_month);

$month_main .= make_col_sm_4('Top languages by number of Articles',$tab_m3);
//---
echo "
  <div class='card'>
    <div class='card-header aligncenter'>
		<div class='card-title' style='margin-bottom: 0rem;' >
			<h3>$month_to_work Leaderboard</h3>
      </div>
    </div>
    <div class='card-body'>
      <div class='row'>
        $month_main
      </div>
    </div>
  </div>
";
//---
echo "
</div>
</section>";
//---
if ($_REQUEST['test'] != '' ) echo "<br>load " . str_replace ( __dir__ , '' , __file__ ) . " true.";
//---
require('foter.php');
//---
?>