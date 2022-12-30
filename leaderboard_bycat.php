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
$key_cat  = 'all';
$get_cat   = $_REQUEST['cat'];
if ($get_cat != '') {    $key_cat = $get_cat;  };
//===
$my_cats = array();
//---
$cats = quary2("select cat from pages group by cat;");
//---
foreach ( $cats AS $Key => $table ) $my_cats[] = $table['cat'];
//---
$my_cats_titles = array();
//---
$qq = quary2('select id, category, display from categories;');
foreach ( $qq AS $k => $tab ) $my_cats_titles[$tab['category']] = $tab['display'];
//---
function cats_start() {
    //---
    global $my_cats, $key_cat, $my_cats_titles;
    //---
    $lines = "";
    //---
    foreach ( $my_cats AS $Key => $cat ) {
        //---
        $active = '';
        //---
        $cat2 = isset($my_cats_titles[$cat]) ? $my_cats_titles[$cat] : $cat;
        //---
        if ($cat == '' ) continue;
        //---
        if ( $key_cat == $cat ) $active = 'active';
        //---
        $lines .= "
	<li class='nav-item menu_item'><a class='nav-link $active' href='leaderboard_bycat.php?cat=$cat'>$cat2</a></li>
		";
        //---
    };
    //---
	$activeold = "";
	//---
	if ( $key_cat == 'all' ) $activeold = "active";
	//---
    $texte = "
	<div class='tab-content'>
		<ul class='nav nav-tabs nav-justified'>
			<li class='nav-item menu_item'><a class='nav-link $activeold' href='leaderboard_bycat.php'>All</a></li>
			$lines
			</ul>
	</div>";
    //---
    return $texte;
    //---
};
//---
echo cats_start();
//===
//=== padding: 2px 0 2px 5px;
function print_cat_table($y) {
  //---
  global $Views_by_users, $sql_users_tab, $Users_word_table, $sql_Languages_tab, $all_views_by_lang, $my_cats_titles;
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
  $y2 = isset($my_cats_titles[$y]) ? $my_cats_titles[$y] : $y;
  //===
  $titlea = "$y2 Leaderboard";
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
echo print_cat_table($key_cat);
//===
if ($get_cat == '') {
    echo print_pinding_table();
};
//===
// echo "<h2 class='text-center'>$get_cat Leaderboard</h2>";
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