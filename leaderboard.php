<?PHP
//---
require('header.php');
require('leader_tables_new.php');
//===
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
  </style>';
//===
$key_year = 'all';
$get_year = $_REQUEST['year'];
$get_month = $_REQUEST['month'];
//===
echo years_start('leaderboard.php');
if ($get_year != '') {
	echo months_start();
};
//===
// echo '<section>';
//===
//===
if ($get_year != '') {    $key_year = $get_year;  };
//=== padding: 2px 0 2px 5px;
// if ($get_month != '') {  echo get_month_table($get_month,$key_year);};
//===
function print_year_table($y) {
  //---
  global $Views_by_users, $sql_users_tab, $Users_word_table, $sql_Languages_tab, $all_views_by_lang;
  //---
  $tab1 = ma_Numbers_table($y);
  //---
  $tables_main = '';
  $tables_main .= make_col_sm_4('Numbers',$tab1, $numb = '3');
  //---
  $tab2 = Make_users_table($Views_by_users[$y], $sql_users_tab[$y], $Users_word_table[$y]);

  $tables_main .= make_col_sm_4('Top users by number of translation', $tab2, $numb = '5');
  //---
  $tab3 = Make_lang_table($sql_Languages_tab[$y],$all_views_by_lang[$y]);

  $tables_main .= make_col_sm_4('Top languages by number of Articles',$tab3);
  //===
  $titlea = "$y Leaderboard";
  if ($y == 'all') {$titlea = 'Leaderboard';};
  //===
  return "
  <div class='card'>
  <div class='card-header aligncenter'>
      <div class='card-title' style='margin-bottom: 0rem;' >
        <h3>$titlea</h3>
      </div>
  </div>
  <div class='card-body'>
      <div class='row'>
        $tables_main
      </div>
  </div>
</div>
  ";
  //===
};
//===
function print_pinding_table(){
  $tbe = Make_Pinding_table();
  //===
  $tab4 = make_col_sm_4('Number of translations',$tbe);
  //===
  return "
  <div class='card'>
    <div class='card-header aligncenter'>
		<div class='card-title' style='margin-bottom: 0rem;' >
			<h3>Translations in process</h3>
      </div>
    </div>
    <div class='card-body'>
      <div class='row'>
        $tab4
      </div>
    </div>
  </div>";
};
//===
echo "\n<div class='container-fluid'>\n";
echo print_year_table($key_year);
//===
if ($get_year == '') {
    echo print_pinding_table();
};
//===
// echo "<h2 class='text-center'>$get_year Leaderboard</h2>";
// echo '<div class="text-center clearfix">';
//===
echo "</div>
<!--</section>-->
"; 
//---
require('foter.php');
//---
if ($_REQUEST['test'] != '' ) echo "<br>load " . str_replace ( __dir__ , '' , __file__ ) . " true.";
//---
?>