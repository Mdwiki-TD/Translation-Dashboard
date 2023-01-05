<?PHP
//---
require('header.php');
require('tables.php');
include_once('functions.php');
require('langcode.php');
//---
$test = $_REQUEST['test'];
// $mainuser = rawurldecode( str_replace ( '_' , ' ' , $username ) );
//---
//---
//---
$views_sql = array();
//---
// views (target, countall, count2021, count2022, count2023, lang)
$quaa_view = "select p.target,v.countall
from pages p,views v
where p.user = '$username'
and p.target = v.target
;";
$views_query = quary2($quaa_view);
//---
$user_total_views = 0;
$user_total_words = 0;
//---
foreach ( $views_query AS $Key => $table ) {
    $countall = $table['countall'];
    $targ = $table['target'];
    $views_sql[$targ] = $countall;
    //---
    $user_total_views = $user_total_views + $countall;
    //---
};
//---
//---
function make_td($tabg,$nnnn) {
    // ------------------
    global $code_to_lang, $Words_table, $views_sql, $user_total_words;
    // ------------------
    $date     = $tabg['date'];
    //---
    //return $date . '<br>';
    //---
    $llang    = $tabg['lang'];
    $md_title = $tabg['title'];
    $cat      = $tabg['cat'];
    $word     = $tabg['word'];
    $targe    = $tabg['target'];
    $pupdate  = isset($tabg['pupdate']) ? $tabg['pupdate'] : '';
    // ------------------
    $views_number = isset($views_sql[$targe]) ? $views_sql[$targe] : '?';
    // ------------------
    $lang2 = isset($code_to_lang[$llang]) ? $code_to_lang[$llang] : $llang;
    //---
    $ccat = make_cat_url( $cat );
    //---
	$word2 = isset($Words_table[$md_title]) ? $Words_table[$md_title] : 0;
	$word = isset($tabg['word']) ? number_format($tabg['word']) : 0;
    //---
    if ( $word < 1 ) $word = $word2;
    //---
    $user_total_words = $user_total_words + $word;
    //---
    $nana = make_mdwiki_title( $md_title );
    //---
    $targe33 = make_target_url( $targe , $llang );
    //---
    $view = make_view_by_number($targe , $views_number, $llang) ;
    //---
    $tran_type = isset($tabg['translate_type']) ? $tabg['translate_type'] : '';
    if ($tran_type == 'all') { 
        $tran_type = 'Whole article';
    };
    //---
    //---
    $laly = "
        <tr>
            <td>$nnnn</td>
            <td><a target='' href='langs.php?langcode=$llang'>$lang2</a></td>
            <td>$nana</td>
            <!-- <td>$date</td> -->
            <td>$ccat</td>
            <td>$word</td>
            <td>$tran_type</td>
            <td>$targe33</td>
            <td>$pupdate</td>
            <td>$view</td>
            <td><a target='' href='../fixwikirefs.php?title=$targe&lang=$llang'>fix</a></td>
        </tr>";
    //---
    return $laly;
};
//---
$quaa = "select * from pages where user = '$username'";
$sql_result = quary2($quaa);
//---
//---
$dd_Pending = array();
$dd = array();
foreach ( $sql_result AS $tait => $tabg ) {
        //---
        $kry = str_replace('-','',$tabg['pupdate']) . ':' . $tabg['lang'] . ':' . $tabg['title'] ;
        //---
        if ( $tabg['target'] != '' ) {
            $dd[$kry] = $tabg;
        } else {
            $dd_Pending[$kry] = $tabg;
        };
        //---
    };
//---
//---
krsort($dd);
// print( count($dd) );
//---
$man = make_mdwiki_user_url($username);
//---
$table2 = "<table class='table table-sm table-striped' style='width:60%;'>
<tr><td>Words: </td><td>$user_total_words</td></tr>
<tr><td>Pageviews: </td><td>$user_total_views</td></tr>
</table>";
//--- 
echo "
<div class='row content'>
	<div class='col-md-4'>$table2</div>
	<div class='col-md-4'><h2 class='text-center'>$man</h2></div>
	<div class='col-md-4'></div>
</div>";
//---
$sato = '
<table class="table table-striped compact soro">
	<thead>
        <tr>
            <th>#</th>
            <th>Language</th>
            <th>Title</th>
            <!--<th>Start date</th> -->
            <th>Category</th>
            <th>Words</th>
            <th>type</th>
            <th>Translated</th>
            <th>Date</th>
			<th>Views</th>
            <th>Fix ref</th>
        </tr>
	</thead>
	<tbody>
		';
//---
$noo = 0;
foreach ( $dd AS $tat => $tabe ) {
    //---
    $noo = $noo + 1;
    $sato .= make_td($tabe,$noo);
    //---
};
//---
$sato .= "
		</tbody>
	</table>";
//---
echo "
<div class='card'>
    <div class='card-body' style='padding:5px 0px 5px 5px;'>
        $sato
    </div>
</div>";
//---
$Pending_a = '';
//---
if ($username == "Mr. Ibrahem") $Pending_a = '<th>Remove</th>';
//---
$pn = "
<table class='table table-striped compact soro'>
    <thead>
        <tr>
            <th>#</th>
            <th>Language</th>
            <th>Title</th>
            <th>Category</th>
            <th>Words</th>
            <th>type</th>
            <th>Translated</th>
            <th>Start date</th>
            <th>Completion</th>
            $Pending_a
        </tr>
    </thead>
    <tbody>
        "; 
//---
$dff = 0;
foreach ( $dd_Pending AS $title=> $kk ) {
    //---
    $dff      = $dff + 1;
    //---
    $lange    = $kk['lang'];
    $lang2    = isset($code_to_lang[$lange]) ? $code_to_lang[$lange] : $lange;
    //---
    // $tran_type = $kk['translate_type'];
    $tran_type = isset($kk['translate_type']) ? $kk['translate_type'] : '';
    if ($tran_type == 'all') { 
        $tran_type = 'Whole article';
    };
    //---
    $md_title = $kk['title'];
    $word     = $kk['word'];
    $lang2    = isset($lang2) ? $lang2 : $lange;
    //---
    $worde = isset($word) ? $word : $Words_table[$md_title];
    $nana = make_mdwiki_title( $md_title );
    //---
    $md_title2 = rawurlencode(str_replace ( ' ' , '_' , $md_title ) );
    $comp = "//$lange.wikipedia.org/wiki/Special:ContentTranslation?page=User%3AMr._Ibrahem%2F" . $md_title2 . "&from=en&to=" . $lange;
    //---
    $rrm = '';
    //---
    $qua = rawurlencode( "delete from pages where user = '$username' and title = '$md_title' and lang = '$lange';" );
    //---
    if ($username == "Mr. Ibrahem") $rrm = "<td><a href='sql.php?code=$qua&pass=yemen&raw=66' target='_blank'>Remove</a></td>"; 
    //---
    $pn .= '
        <tr>
            <td>' . $dff  .'</td>
            <td><a target="" href="langs.php?langcode=' . $lange . '">' . $lang2 . '</a>' . '</td>
            <td>' . $nana .'</td>
            <td>' . make_cat_url( $kk['cat'] ) .'</td>
            <td>' . $worde . '</td>
            <td>' . $tran_type . '</td>
            <td>Pending</td>
            <td>' . $kk['date'] .'</td>' . 
            "<td><a href='$comp'>Completion</a></td>
            $rrm
        </tr>"; 
    //---
};
//---
$pn .= '
    </tbody>
</table>';
//---
print "
<br>
<div class='card'>
	<div class='card-body' style='padding:5px 0px 5px 5px;'>
	<h2 class='text-center'>Translations in process</h2>
	$pn
	</div>
</div>";
//---
require('foter.php');
//---

?>