<?PHP
//--------------------
require('header1.php');
require('tables.php');
require('functions.php');
require('langcode.php');
//--------------------
$mainlang = $_REQUEST['langcode'];
$mainlang = rawurldecode( str_replace ( '_' , ' ' , $mainlang ) );
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
print '';
print '<div class="col-md-10 col-md-offset-1" align=left >';
print "<h1 class='text-center'>$printlang</h1>";
print '<div class="text-center clearfix leaderboard" >';
//==========================
$table_leg = '  <table class="sortable table table-striped alignleft">
        <tr>
            <th onclick="sortTable(0)">#</th>
            <th onclick="sortTable(1)">User</th>
            <th onclick="sortTable(2)">Title</th>
            <!--<th onclick="sortTable(3)">Start date</th> -->
            <th onclick="sortTable(4)">Category</th>
            <th onclick="sortTable(5)">Words</th>
            <th onclick="sortTable(6)">Translated</th>
';
//--------------------
//==========================
$qua_views = "select 
#p.title, p.user, p.date, p.word, p.lang, p.cat, p.pupdate, 
p.target, v.count

from pages p,views v
where p.lang = '$mainlang'
and p.lang = v.lang
and p.target = v.target
;
";
$views_quary = quary2($qua_views);
//---
$table_of_views = array();
$lang_total_views = 0;
//----
foreach ( $views_quary AS $Key => $tablea ) {
    $Counte = $tablea['count'];
    $tat = $tablea['target'];
    $table_of_views[$tat] = $Counte;
    //--------------------
    $lang_total_views = $lang_total_views + $Counte;
    //--------------------
    };
//==========================
$vase = 'Pageviews';
if ( $lang_total_views != 0) { $vase .= "<br>($lang_total_views)"; };
//--------------------------
$table_leg .= "
<th onclick='sortTable(7)'>Completion date</th>
<th onclick='sortTable(8)'>$vase</th>
</tr>";
//==========================
$quae = "select * from pages where lang = '$mainlang'";
$mains = quary2($quae);
//==========================
$mamo = array();
$mamo_Pending = array();
//--------------------
foreach ( $mains AS $yhu => $Taab ) {
	//--------------------
	$dat1 = isset($Taab['pupdate']) ? $Taab['pupdate'] : '';
	$dat2 = isset($Taab['date']) ? $Taab['date'] : '';
	$dat = $dat1 != '' ? $dat1 : $dat2;
	$urt = '';
	if ($dat != '') {
		$urt = str_replace('-','',$dat) . ':';
	};
	$kry = $urt . $Taab['lang'] . ':' . $Taab['title'] ;
	//--------------------
	if ( $Taab['target'] != '' ) {
		$mamo[$kry] = $Taab;
	} else {
		$mamo_Pending[$kry] = $Taab;
	};
	//--------------------
};
//--------------------
//==========================
//--------------------
function make_td_na($tabb,$number) {
    // ------------------
    global $table_of_views;
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
    $ccat = make_cat_url($cat);
    //--------------------
    $target_link = make_target_url($target , $lang);
    //--------------------
    $use = rawurlEncode($user);
    $use = str_replace ( '+' , '_' , $use );
    //--------------------
    $laly = '
    <tr>
        <td>' . $number . '</td>
        <td><a target="" href="users.php?user=' . $use . '"><span style="white-space: nowrap;">' . $user . '</span></a>' . '</td>
        <td>' . $nana . '</td>
        <!-- <td>' . $date  . '</td> -->
        <td>' . $ccat . '</td>
        <td>' . $word . '</td>
        <td>' . $target_link . '</td>
        ';
    //--------------------
    $view_number = isset($table_of_views[$target]) ? $table_of_views[$target] : '?';
    $view = make_view_by_number($target , $view_number);
    //--------------------
    $laly .= '
    <td>' . $pupdate . '</td>
    <td>' . $view . '</td>
    </tr>';
    //--------------------
    return $laly;
    //--------------------
};
//--------------------
//==========================
$sato = $table_leg . "
";
//==========================
//--------------------
$n = 0;
//--------------------
krsort($mamo);
//--------------------
foreach ( $mamo AS $uuu => $tabb ) {
    // $tabb = $mains[$title];
    $n = $n + 1 ;
    $sato .= make_td_na($tabb,$n);
};
//--------------------
$sato .= '</table>';
print $sato;
//--------------------
//==========================
$Pending_table = '  <table class="sortable table table-striped alignleft">
        <tr>
            <th onclick="sortTable(0)">#</th>
            <th onclick="sortTable(1)">User</th>
            <th onclick="sortTable(2)">Title</th>
            <th onclick="sortTable(3)">Start date</th>
            <th onclick="sortTable(4)">Category</th>
            <th onclick="sortTable(5)">Words</th>
            <th onclick="sortTable(6)">Translated</th>
        </tr>
';
//--------------------
print '</div>
<div class="text-center clearfix leaderboard" >
<h1 class="text-center">Translations in process</h1>
';
//--------------------
krsort($mamo_Pending);
//--------------------
$n_Pending = 0;
//--------------------
foreach ( $mamo_Pending AS $uuu => $tabb ) {
    //--------------------
    $mdtitle = $tabb['title'];
    $user    = $tabb['user'];
    $date    = $tabb['date'];
    $word    = $tabb['word'];
    $lang    = $tabb['lang'];
    $cat     = $tabb['cat'];
    //--------------------
    $nana = make_mdwiki_title( $mdtitle );
    $ccat = make_cat_url($cat);
    //--------------------
    $n_Pending = $n_Pending + 1 ;
    //--------------------
    $use = rawurlEncode($user);
    $use = str_replace ( '+' , '_' , $use );
    //--------------------
    $nady = '
    <tr>
        <td>' . $n_Pending . '</td>
        <td><a target="" href="users.php?user=' . $use . '"><span style="white-space: nowrap;">' . $user . '</span></a>' . '</td>
        <td>' . $nana . '</td>
        <td>' . $date  . '</td>
        <td>' . $ccat . '</td>
        <td>' . $word . '</td>
        <td>Pending</td>
    </tr>
        ';
    $Pending_table .= $nady . '';
    //--------------------
};
//--------------------
$Pending_table .= '</table>';
print $Pending_table;
//--------------------
print "</div>";
//--------------------
require('foter1.php');
print "</div>"
//--------------------
?>