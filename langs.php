<?PHP
//--------------------
require('header1.php');
require('tables.php');
require('functions.php');
//--------------------
$mainlang = $_REQUEST['langcode'];
$mainlang = rawurldecode( str_replace ( '_' , ' ' , $mainlang ) );
//--------------------
print '';
print '<div class="col-md-10 col-md-offset-1" align=left >';
//--------------------
$man = $mainlang;
$langname = isset($code_to_lang[$mainlang]) ? $code_to_lang[$mainlang] : $mainlang;
//==========================
$printlang = $langname;
//==========================
if ( $_SERVER['SERVER_NAME'] != 'mdwiki.toolforge.org' ) { 
    $printlang .= ' <a target="_blank" href="http://' . $mainlang . '.wikipedia.org/wiki/Category:Translated_from_MDWiki">(cat)</a>';
};
//==========================
print "<h1 class='text-center'>$printlang</h1>";
//==========================
print '<div class="text-center clearfix leaderboard" >';
//==========================
$table_leg = '<table class="sortable table table-striped alignleft">
';
//--------------------
//<th style="text-align:center; vertical-align: middle;" onclick="sortTable(0)">#</th>
$table_leg .= '<tr>
<th onclick="sortTable(0)">#</th>
<th onclick="sortTable(1)">User</th>
<th onclick="sortTable(2)">Title</th>
<th onclick="sortTable(3)">Start date</th>
<th onclick="sortTable(4)">Category</th>
<th onclick="sortTable(5)">Words</th>
<th onclick="sortTable(6)">Translated</th>
';
//--------------------
//==========================
$sato_Pending = $table_leg . '</tr>';
$sato = $table_leg . '
<th onclick="sortTable(7)">Completion date</th>
<th onclick="sortTable(8)">Pageviews</th>
</tr>';
//==========================
$quae = "select * from pages where lang = '$mainlang'";
$sql_u = quary($quae);
//==========================
$mains = array();
//--------------------
$n = 0;
//--------------------
foreach ( $sql_u AS $id => $row ) {
    $ff = array();
    $n = $n + 1 ;
    foreach ( $row AS $nas => $value ) {
        $ff[$nas] = $value;
    };
    $mains[$n] = $ff;
};
//==========================
//--------------------
$n = 0;
$n_Pending = 0;
//--------------------
foreach ( $mains AS $title => $tabb ) {
    //--------------------
    //$tabb = {"title": "Flucloxacillin", "user": "Wakkie1379", "cat": "", "date": "2021-May-13", "lang": "ja", "word": 183}
    //--------------------
    $mdtitle = $tabb['title'];
    $user    = $tabb['user'];
    $date    = $tabb['date'];
    $word    = $tabb['word'];
    $lang    = $tabb['lang'];
    $cat     = $tabb['cat'];
    $pupdate = $tabb['pupdate'];
    $target  = $tabb['target'];
    //--------------------
    $nana = make_mdwiki_title( $mdtitle );
    $ccat = make_cat_url ($cat);
    //--------------------
    $target_link = make_target_url ($target , $lang);
    //--------------------
    $target2 = '';
    $nnnn = 0;
    //--------------------
    if ($target_link != '') {
        $n = $n + 1 ;
        $nnnn = $n;
        $target2 = $target_link;
    } else {
        $n_Pending = $n_Pending + 1 ;
        $nnnn = $n_Pending;
        $target2 = 'Pending';
    };
    //--------------------
    $use = rawurlEncode($user);
    $use = str_replace ( '+' , '_' , $use );
    //--------------------
    $nady = '
    <tr>
        <td>' . $nnnn . '</td>
        <td><a target="" href="users.php?user=' . $use . '"><span style="white-space: nowrap;">' . $user . '</span></a>' . '</td>
        <td>' . $nana . '</td>
        <td>' . $date . '</td>
        <td>' . $ccat . '</td>
        <td>' . $word . '</td>
        <td>' . $target2 . '</td>
        ';
    //--------------------
    if ($target_link != '') {
        $view = make_view($target , $lang) ;
        $sato .= $nady . '
        <td>' . $pupdate . '</td>
        <td>' . $view . '</td>
        </tr>';
    } else {
        $sato_Pending .= $nady . '</tr>';
    };
    //--------------------
};
//--------------------
$sato .= '</table>';
print $sato;
//--------------------
//--------------------
print "</div>";
print '<div class="text-center clearfix leaderboard" >';
print '<h1 class="text-center">Translations in process</h1>';
//--------------------
$sato_Pending .= '</table>';
print $sato_Pending;
print "</div>";
//--------------------
?>
<?PHP
//--------------------
//--------------------
require('foter1.php');
print "</div>"
//--------------------

?>