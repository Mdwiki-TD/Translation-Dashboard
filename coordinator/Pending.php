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

<h4>Translations in process</h4>
<?PHP
//---
/*

صفحات مكررة

SELECT A.id as id1, A.title as title1, A.user as user1, A.target as target1,
B.id as id2, B.title as title2, B.user as user2, B.target as target2
 from pages A, pages B
where A.target = ''
and A.lang = B.lang
and A.title = B.title
and B.target != ''
;

للحذف:
SELECT A.id from pages A, pages B where A.target = '' and A.lang = B.lang and A.title = B.title and B.target != '';

*/
//---
require('leader_tables_new.php');
//---
echo '';
//---
$key_cat  = 'all';
$get_cat   = $_REQUEST['cat'];
if ($get_cat != '') {    $key_cat = $get_cat;  };
//---
$my_cats = array();
//---
$cats = quary2("select cat from pages group by cat;");
//---
foreach ( $cats AS $Key => $table ) $my_cats[] = $table['cat'];
//---
$my_cats_titles = array();
//---
$qq = quary2('select id, category, display, depth from categories;');
foreach ( $qq AS $k => $tab ) $my_cats_titles[$tab['category']] = $tab['display'];
//---

function Make_Pinding_table() {
    //---
    global $user_process_tab;
    //---
    $text = "
    <table class='table table-striped compact soro'>
	<thead>
		<tr>
			<th>#</th>
			<th class='spannowrap'>User</th>
			<th>Articles</th>
		</tr>
    </thead>
    <tbody>
    ";
    //---
    $text .= '
    ';
    //---
    arsort($user_process_tab);
    //---
	$n = 0;
    //---
    foreach ( $user_process_tab AS $user => $pinde ) {
        if ($user != 'test' && $user != '') {
            //---
			$n ++;
            //---
            $use = rawurlEncode($user);
            $use = str_replace ( '+' , '_' , $use );
            //---
            if ($pinde > 0 ) {
                //---
                $text .= "
                <tr>
                    <td>$n</td>
                    <td><a href='users.php?user=$use'>$user</a></td>
                    <td>$pinde</td>
                </tr>
                ";
            };
        };
    };
    //---
    $text .= '
    </tbody>
    </table>';
    //---
    return $text;
}
//---
$tbe = Make_Pinding_table();
//---
echo $tbe;
//---
?>