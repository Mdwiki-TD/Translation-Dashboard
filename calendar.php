<?PHP
//--------------------
require('header.php');
require('tables.php');
include_once('functions.php');
require('tables_calendar.php');
//--------------------
//==========================
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
//==========================
echo years_start();
echo months_start();
//==========================
$Month_Page_views = 0;
$Views_for_month_by_users = array();
$Views_by_lang_for_month = array();
//==========================
$get_month = $_REQUEST['month'];
$last_month = '';
$month_to_work = $get_month != '' ? $get_month : $last_month;   #2021-12
//==========================
function make_calendar_arrays($month_to_work) {
    //------------
    global $Month_Page_views, $Views_for_month_by_users, $Views_by_lang_for_month;
    //------------
    $table_name = '';                           # views_by_month_22
    $month_col  = '';                           # v_2022_02
    //------------
    if (preg_match_all('/20([0-9]{2})-([0-9]{2})/', $month_to_work, $matches)) {
        $y = $matches[1][0];
        $table_name = 'views_by_month_' . $y;
        $month_col  = 'v_20' . $y . '_' . $matches[2][0];   #v_2022_02
    };
    //------------
    $sqlqua = "select p.target, p.user, v.lang, 
    v." . $month_col . "
    from pages p, " . $table_name . " v
    where p.lang = v.lang
    and p.target = v.target
    ;
    ";
    //--------------------
    // echo '<h1>' . $sqlqua . '</h1>';
    //--------------------
    $sql_cu = quary2($sqlqua);
    //--------------------
    //==========================
    foreach ($sql_cu as $id => $row) {
        $target = $row['target'];
        $views = $row[$month_col];
        //--------------------
        $Month_Page_views += $views;
        //==========================
        $user = $row['user'];
        $yhy = isset($Views_for_month_by_users[$user]) ? $Views_for_month_by_users[$user] : "";
        if ($yhy == '') { $Views_for_month_by_users[$user] = 0; };
        //--------------------
        $Views_for_month_by_users[$user] = $Views_for_month_by_users[$user] + $views;
        //==========================
        $lang = $row['lang'];
        $hah = isset($Views_by_lang_for_month[$lang]) ? $Views_by_lang_for_month[$lang] : "";
        if ($hah == '') { $Views_by_lang_for_month[$lang] = 0; };
        //--------------------
        $Views_by_lang_for_month[$lang] = $Views_by_lang_for_month[$lang] + $views;
    };
    //==========================
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
    //==========================
};
//==========================
make_calendar_arrays($month_to_work);
//==========================
echo '<section>
<div class="container">';
//==========================
$month_main = '';
//==========================
$tab_m1 = Make_Numbers_table(
    count($Count_users_cal[$month_to_work]), 
    number_format($Count_articles_cal[$month_to_work]), 
    number_format($Total_words_cal[$month_to_work]), 
    count($Count_langs_cal[$month_to_work]),
    $Month_Page_views
);
$month_main .= make_col_sm_4('Numbers',$tab_m1, $numb = '3');
//==========================
$tab_m2 = Make_users_table_cal($Views_for_month_by_users,$Count_users_cal[$month_to_work],$Users_words_cal[$month_to_work]);

$month_main .= make_col_sm_4('Top users by number of translation', $tab_m2, $numb = '5');
//==========================
$tab_m3 = Make_lang_table_cal($Count_langs_cal[$month_to_work],$Views_by_lang_for_month);

$month_main .= make_col_sm_4('Top languages by number of Articles',$tab_m3);
//==========================
echo "
  <div class='panel panel-default'>
    <div class='panel-heading aligncenter'>
      <div class='panel-title' style='font-size:200%;'>
        $month_to_work Leaderboard
      </div>
    </div>
    <div class='panel-body'>
      <div class='row'>
        $month_main
      </div>
    </div>
  </div>";
//==========================
echo "
</div>
</section>";
//--------------------
require('foter.php');
//--------------------
?>