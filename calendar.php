<?PHP
//--------------------
require('header.php');
require('tables.php');
include_once('functions.php');
// require('langcode.php');
require('calendar_tables.php');
//--------------------
$test = $_REQUEST['test'];
//--------------------
//==========================
$get_month = $_REQUEST['month'];
$last_month = '';
//----
$views_sql = array();
//--------------------
//==========================
$month_to_work = $get_month != '' ? $get_month : $last_month;
//==========================
//--------------------
//==========================
print '
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
//==========================
print years_start();
print months_start();
//==========================
print '
<section class="container" style="margin-left:20px;margin-left:20px;">';
//==========================
print "<h1 class='text-center'>$month_to_work Leaderboard</h1>";
print '<div class="text-center clearfix">';
//==========================
print '<span class="colsm4" style="width:20%;">';
print Make_Numbers_table(
    count($Count_users_cal[$month_to_work]), 
    number_format($Count_articles_cal[$month_to_work]), 
    number_format($Total_words_cal[$month_to_work]), 
    count($Count_langs_cal[$month_to_work])
);
print '</span>';
//--------------------
//==========================
print'<span class="colsm4" style="width:45%;">';
print "<h3>Top users by number of translations</h3>";

print'<div style="max-height:300px; overflow: auto; padding: 2px 0 2px 5px; background-color:transparent;vertical-align:top;font-size:100%">';
// print Make_users_table_cal($Views_by_users[$month_to_work],$Count_users_cal[$month_to_work],$Users_words_cal[$month_to_work]);
print Make_users_table_cal(array(),$Count_users_cal[$month_to_work],$Users_words_cal[$month_to_work]);
print '</div>';

print '</span>';
//==========================
//==========================
print'<span class="colsm4" style="width:35%;">';
// print Make_lang_table_cal($Count_langs_cal[$month_to_work],$all_views_by_lang[$month_to_work]);
print Make_lang_table_cal($Count_langs_cal[$month_to_work],array());
print '</span>';
//==========================

//--------------------
//==========================
print "
</div>
</section>
</main>
<!-- Footer 
<footer class='app-footer'>
</footer>
-->
</body>
</html>
</div>"
//--------------------
?>