<?PHP
//---
//---
if ($_REQUEST['test'] != '') {
    // echo(__file__);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
require('header.php');
//---
require('tables.php');
include_once('functions.php');
require('langcode.php');
//---
$test = isset($_GET['test']) ? $_GET['test'] : '';
$mainlang = $_REQUEST['langcode'];
$mainlang = rawurldecode( str_replace ( '_' , ' ' , $mainlang ) );
//---
$man = $mainlang;
$langname = isset($code_to_lang[$mainlang]) ? $code_to_lang[$mainlang] : $mainlang;
//---
$printlang = $langname;
//---
if ( $_SERVER['SERVER_NAME'] == 'localhost' || $test != '' ) { 
    $printlang .= ' <a target="_blank" href="http://' . $mainlang . '.wikipedia.org/wiki/Category:Translated_from_MDWiki">(cat)</a>';
};
//---
//---
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
$lang_total_words = 0;
$lang_total_views = 0;
//---
foreach ( $views_quary AS $Key => $tablea ) {
    //---
    $countall = $tablea['countall'];
    //---
    $tat = $tablea['target'];
    $table_of_views[$tat] = $countall;
    //---
    $lang_total_views = $lang_total_views + $countall;
    //---
    };
//---
$quae = "select * from pages where lang = '$mainlang'";
$mains = quary2($quae);
//---
$mamo = array();
$mamo_Pending = array();
//---
foreach ( $mains AS $yhu => $Taab ) {
	//---
	$dat1 = isset($Taab['pupdate']) ? $Taab['pupdate'] : '';
	$dat2 = isset($Taab['date']) ? $Taab['date'] : '';
	$dat = isset($dat1) ? $dat1 : $dat2;
	$urt = '';
	if ($dat != '') {
		$urt = str_replace('-','',$dat) . ':';
	};
	$kry = $urt . $Taab['lang'] . ':' . $Taab['title'] ;
	//---
	if ( $Taab['target'] != '' ) {
		$mamo[$kry] = $Taab;
	} else {
		$mamo_Pending[$kry] = $Taab;
	};
	//---
};
//---
function make_td_na($tabb,$number) {
    //---
    global $table_of_views, $Words_table, $lang_total_words;
    //---
    //$tabb = {"title": "Flucloxacillin", "user": "Wakkie1379", "cat": "", "date": "2021-May-13", "lang": "ja", "word": 183}
    //---
    $mdtitle = $tabb['title'];
    $user    = $tabb['user'];
    $date    = $tabb['date'];
    $lang    = $tabb['lang'];
    $cat     = $tabb['cat'];
    $pupdate = $tabb['pupdate'];
    $target  = $tabb['target'];
    //---
	$word2 = isset($Words_table[$mdtitle]) ? $Words_table[$mdtitle] : 0;
	$word = isset($tabb['word']) ? number_format($tabb['word']) : 0;
    //---
    if ( $word < 1 ) $word = $word2;
    //---
    $lang_total_words = $lang_total_words + $word;
    //---
    $nana = make_mdwiki_title( $mdtitle );
    $ccat = make_cat_url($cat);
    //---
    $target_link = make_target_url($target , $lang);
    //---
    $use = rawurlEncode($user);
    $use = str_replace ( '+' , '_' , $use );
    //---
    // $tran_type = $tabb['translate_type'];
    $tran_type = isset($tabb['translate_type']) ? $tabb['translate_type'] : '';
    if ($tran_type == 'all') { 
        $tran_type = 'Whole article';
    };
    //---
    //---
    $year = substr($date,0,4);
    //---
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
    //---
    $view_number = isset($table_of_views[$target]) ? $table_of_views[$target] : '?';
    $view = make_view_by_number($target , $view_number , $lang, $pupdate);
    //---
    $laly .= '
    <td class="spannowrap">' . $pupdate . '</td>
    <td>' . $view . '</td>
    </tr>';
    //---
    return $laly;
    //---
};
//---
krsort($mamo);
//---
$lalo = '';
$n = 0;
//---
foreach ( $mamo AS $uuu => $tabb ) {
    $n = $n + 1 ;
    $lalo .= make_td_na($tabb,$n);
};
//---
$lalo = "
<table class='table table-striped compact soro'>
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Title</th>
            <!--<th>Start date</th> -->
            <th>Category</th>
            <th>Words</th>
            <th>type</th>
            <th>Translated</th>
            <th>Date</th>
            <th>Views</th>
        </tr>
    </thead>
    <tbody>
		$lalo
    </tbody>
</table>
";
//---

//---
$table2 = "<table class='table table-sm table-striped' style='width:70%;'>
<tr><td>Words: </td><td>$lang_total_words</td></tr>
<tr><td>Pageviews: </td><td><span id='hrefjsontoadd'>$lang_total_views</span></td></tr>

</table>";
//---
echo "
<div class='row content'>
	<div class='col-md-4'>$table2</div>
	<div class='col-md-4'><h2 class='text-center'>$printlang</h2></div>
	<div class='col-md-4'></div>
</div>";
//---
print "
<div class='card'>
	<div class='card-body' style='padding:5px 0px 5px 5px;'>
		$lalo
	</div>
</div>
";
//---
$Pending_table = '';
//---
krsort($mamo_Pending);
//---
$n_Pending = 0;
//---
foreach ( $mamo_Pending AS $uuu => $tabb ) {
    //---
    $mdtitle = $tabb['title'];
    $user    = $tabb['user'];
    $date    = $tabb['date'];
    $lang    = $tabb['lang'];
    $cat     = $tabb['cat'];
    //---
	$word2 = isset($Words_table[$mdtitle]) ? $Words_table[$mdtitle] : 0;
	$word = isset($tabb['word']) ? number_format($tabb['word']) : 0;
    //---
    if ( $word < 1 ) $word = $word2;
    //---
    // $tran_type = $tabb['translate_type'];
    $tran_type = isset($tabb['translate_type']) ? $tabb['translate_type'] : '';
    if ($tran_type == 'all') { 
        $tran_type = 'Whole article';
    };
    //---
    $nana = make_mdwiki_title( $mdtitle );
    $ccat = make_cat_url($cat);
    //---
    $n_Pending = $n_Pending + 1 ;
    //---
    $use = rawurlEncode($user);
    $use = str_replace ( '+' , '_' , $use );
    //---
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
    //---
};
//---
$Pending_table = "
<table class='table table-striped compact soro'>
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Title</th>
            <th>Category</th>
            <th>Words</th>
            <th>type</th>
            <th>Start date</th>
            <th>Translated</th>
        </tr>
    </thead>
    <tbody>
		$Pending_table
    </tbody>
</table>
";
//---
print "
<br>
<div class='card'>
	<div class='card-body' style='padding:5px 0px 5px 5px;'>
	<h2 class='text-center'>Translations in process</h2>
	$Pending_table
	</div>
</div>";
//---
if ($_REQUEST['test'] != '' ) echo "<br>load " . str_replace ( __dir__ , '' , __file__ ) . " true.";
//---
?>
<?PHP
//---
require('foter.php');
//---
?>