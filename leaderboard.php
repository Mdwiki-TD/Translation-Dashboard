<?PHP
//--------------------
require('header.php');
require('leader_tables_2021.php');
//--------------------
//==========================
print '
<style>
.colsm4{
    position: relative;
    min-height: 1px;
}

@media (min-width:1200px) {
    .colsm4{
      padding-right: 35px;
      padding-left: 35px
    }
}
@media (min-width:768px) {
    .colsm4{
      float: left;
      width: 33.33333333%
    }
}
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
<section class="container" style="margin-left:20px;margin-left:20px;">
<h1 class="text-center">Leaderboard</h1>
<div class="text-center clearfix">
';
//==========================
print '<span class="colsm4" style="width:20%;">';
print $Numbers_table;
print '</span>';
//--------------------
//==========================
print'<span class="colsm4" style="width:45%;">';
print "<h3>Top users by number of translations</h3>";

print'<div style="max-height:300px; overflow: auto; padding: 2px 0 2px 5px; background-color:transparent;vertical-align:top;font-size:100%">';
print Make_users_table($Views_by_users['all'],$sql_users_tab['all'],$Users_word_table['all']);
print '</div>';

print '</span>';
//==========================
//==========================
print'<span class="colsm4" style="width:35%;">';
print Make_lang_table();
print '</span>';
//==========================
//--------------------
print '
</div>
';
//==========================
print '<br/>
<h1 class="text-center">Statistics by year</h1>
<div class="text-center clearfix">';
print'<span class="colsm4" style="width:45%;">';
print "<h3>2021</h3>";
// print Make_users_table($Views_by_users['2021'],$sql_users_tab['2021'],$Users_word_table['2021']);
print GET_2021_USERS_TABLE();
print '</span>';

print'<span class="colsm4" style="width:45%;">';
print "<h3>2022</h3>";
print Make_users_table($Views_by_users['2022'],$sql_users_tab['2022'],$Users_word_table['2022']);
print '</span>';

print '</div>
';
//==========================
print '<br/>';
print '<h1 class="text-center">Translations in process</h1>';
//==========================
print '<span class="colsm4" style="width:33%;">';
print Make_Pinding_table();
print "</span>";
//==========================
print "</div>";
print "
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