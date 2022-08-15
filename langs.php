<?PHP
//--------------------
require('header.php');
require('tables.php');
include_once('functions.php');
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
if ( $_SERVER['SERVER_NAME'] == 'localhost' ) { 
    $printlang .= ' <a target="_blank" href="http://' . $mainlang . '.wikipedia.org/wiki/Category:Translated_from_MDWiki">(cat)</a>';
};
//==========================
print '';
// print '<div class="col-md-10 col-md-offset-1" align=left >';
print '<div style="margin-right:25px;margin-left:25px;boxSizing:border-box;" align=left >';
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
            <th onclick="sortTable(6)">Translate type</th>
            <th onclick="sortTable(7)">Translated</th>
';
//--------------------
//==========================
// views (target, countall, count2021, count2022, count2023, lang)
$qua_views = "select 
#p.title, p.user, p.date, p.word, p.lang, p.cat, p.pupdate, 
p.target, v.countall

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
    //--------------------
    $countall = $tablea['countall'];
    //--------------------
    $tat = $tablea['target'];
    $table_of_views[$tat] = $countall;
    //--------------------
    $lang_total_views = $lang_total_views + $countall;
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
    // $tran_type = $tabb['translate_type'];
    $tran_type = isset($tabb['translate_type']) ? $tabb['translate_type'] : '';
    if ($tran_type == 'all') { 
        $tran_type = 'Whole article';
    };
    //--------------------
    //--------------------
    $year = substr($date,0,4);
    //--------------------
    $laly = '
    <tr class="filterDiv show2 ' . $year . '">
        <td>' . $number . '</td>
        <td><a target="" href="users.php?user=' . $use . '"><span style="white-space: nowrap;">' . $user . '</span></a>' . '</td>
        <td>' . $nana . '</td>
        <!-- <td>' . $date  . '</td> -->
        <td>' . $ccat . '</td>
        <td>' . $word . '</td>
        <td>' . $tran_type . '</td>
        <td>' . $target_link . '</td>
        ';
    //--------------------
    $view_number = isset($table_of_views[$target]) ? $table_of_views[$target] : '?';
    $view = make_view_by_number($target , $view_number , $lang);
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
$lalo = $table_leg . "
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
    $lalo .= make_td_na($tabb,$n);
};
//--------------------
$lalo .= '</table>';
//--------------------
require("filter-table.php");
//--------------------
print $lalo;
//--------------------
//==========================
$Pending_table = '  <table class="sortable table table-striped alignleft">
        <tr>
            <th onclick="sortTable(0)">#</th>
            <th onclick="sortTable(1)">User</th>
            <th onclick="sortTable(2)">Title</th>
            <th onclick="sortTable(3)">Category</th>
            <th onclick="sortTable(4)">Words</th>
            <th onclick="sortTable(5)">Translate type</th>
            <th onclick="sortTable(6)">Start date</th>
            <th onclick="sortTable(7)">Translated</th>
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
    // $tran_type = $tabb['translate_type'];
    $tran_type = isset($tabb['translate_type']) ? $tabb['translate_type'] : '';
    if ($tran_type == 'all') { 
        $tran_type = 'Whole article';
    };
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
        <td>' . $ccat . '</td>
        <td>' . $word . '</td>
        <td>' . $tran_type . '</td>
        <td>' . $date  . '</td>
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
require('foter.php');
print "</div>"
//--------------------
?>