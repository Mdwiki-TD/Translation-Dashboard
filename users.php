<?PHP
//---
require('header.php');
//---
if ($_GET['test'] != '') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

require('tables.php');
include_once('functions.php');
require('langcode.php');
//---
function print_mem() {
    /* Currently used memory */
    $mem_usage = memory_get_usage();
    
    /* Peak memory usage */
    $mem_peak = memory_get_peak_usage();

    echo '<br>The script is now using: <strong>' . round($mem_usage / 1024) . 'KB</strong> of memory.';
    echo 'Peak usage: <strong>' . round($mem_peak / 1024) . 'KB</strong> of memory.<br>';
};
//---
if ($_GET['test'] != '') print_mem();
//---
$user_views_sql = array();
$user_total_words = 0;
$user_total_views = 0;
$dd_Pending = array();
//---
function make_td_fo_user($tabg, $nnnn) {
    //---
    global $code_to_lang, $Words_table, $user_views_sql, $user_total_words, $user_total_views;
    //---
    $date     = $tabg['date'];
    //---
    //return $date . '<br>';
    //---
    $llang    = $tabg['lang'];
    $md_title = $tabg['title'];
    $cat      = $tabg['cat'];
    $targe    = $tabg['target'];
    $pupdate  = isset($tabg['pupdate']) ? $tabg['pupdate'] : '';
    //---
    // $views_number = isset($user_views_sql[$targe]) ? $user_views_sql[$targe] : 0;
    $views_number = 0;
    $user_total_views += $views_number;
    //---
    // $lang2 = isset($code_to_lang[$llang]) ? $code_to_lang[$llang] : $llang;
    $lang2 = $llang;
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
    $view = make_view_by_number($targe, $views_number, $llang, $pupdate) ;
    //---
    $tran_type = isset($tabg['translate_type']) ? $tabg['translate_type'] : '';
    if ($tran_type == 'all') { 
        $tran_type = 'Whole article';
    };
    //---
    $year = substr($pupdate,0,4);
    //---
    $laly = '
        <tr class="filterDiv show2 ' . $year . '">
            <td>' . $nnnn   . '</td>
            <td><a target="" href="langs.php?langcode=' . $llang . '">' . $lang2 . '</a>' . '</td>
            <td>' . $nana  . '</td>
            <!-- <td>' . $date  . '</td> -->
            <td>' . $ccat  . '</td>
            <td>' . $word . '</td>
            <td>' . $tran_type . '</td>
            <td>' . $targe33 . '</td>
            <td class="spannowrap">' . $pupdate . '</td>
            <td data-sort="0">' . $view . '</td>
        </tr>
        '; 
    //---
    return $laly;
};
//---
function make_user_table($user_main, $test, $limit) {
    //---
    global $code_to_lang, $Words_table, $user_views_sql, $lang_code_to_en;
    global $user_total_views, $user_total_words, $dd_Pending;
    //---
    $user_main = rawurldecode( str_replace ( '_' , ' ' , $user_main ) );
    //---
    $count_sql = "select count(title) as count from pages where user = '$user_main';";
    //---
    $count_query = quary2($count_sql);
    //---
    $user_count = $count_query[1]['count'];
    //---
    unset($count_query);
    //---
    if ($test != '' ) echo "<br>user_count : $user_count<br>";
    //---
    $done = 0;
    $offset = 0;
    //---
    while ($done < $user_count) {
        // echo "offset: $offset.";
        // views (target, countall, count2021, count2022, count2023, lang)
        $quaa_view = "select p.target,v.countall
        from pages p,views v
        where p.user = '$user_main'
        and p.target = v.target
        limit 200
        offset $offset
        ;
        ";
        //---
        $views_query = quary2($quaa_view);
        //---
        if (count($views_query) == 0) { $done = $user_count;};
        //---
        foreach ( $views_query AS $Key => $table ) {
            $countall = $table['countall'];
            $targ = $table['target'];
            $user_views_sql[$targ] = $countall;
            //---
            // $user_total_views = $user_total_views + $countall;
            //---
            $done += 1;
            //---
        };
        //---
        unset($views_query);
        //---
        $offset += 200;
        //---
    };
    //---
    $quaa = "select * from pages where user = '$user_main'";
    //---
    if ($limit != '' && is_numeric($limit)) $quaa = $quaa . " limit $limit";
    //---
    if ($test != '') echo $quaa;
    //---
    $sql_result = quary2($quaa);
    //---
    $dd = array();
    foreach ( $sql_result AS $tait => $tabg ) {
            //---
            // $kry = str_replace('-','',$tabg['pupdate']) . ':' . $tabg['target'] ;
            $kry = str_replace('-','',$tabg['pupdate']) . ':' . $tabg['lang'] . ':' . $tabg['title'] ;
            //print $kry . '<br>';
            //---
            if ( $tabg['target'] != '' ) {
                $dd[$kry] = $tabg;
            } else {
                $dd_Pending[$kry] = $tabg;
            };
            //---
        };
    //---
    krsort($dd);
    // print( count($dd) );
    //---
    $sato = '  
    <table class="table table-striped compact soro">
        <thead>
            <tr>
                <th>#</th>
                <th>Lang.</th>
                <th>Title</th>
                <!--<th>Start date</th> -->
                <th>Category</th>
                <th>Words</th>
                <th>type</th>
                <th>Translated</th>
                <th>Date</th>
                <th>Pageviews</th>
            </tr>
        </thead>
        <tbody>';
    //---
    $noo = 0;
    foreach ( $dd AS $tat => $tabe ) {
        //---
        $noo = $noo + 1;
        $sato .= make_td_fo_user($tabe,$noo);
        //---
    };
    //---
    $sato .= '
        </tbody>
	</table>';
    //---
    $man = make_mdwiki_user_url($user_main);
    //---
    $table2 = "<table class='table table-sm table-striped' style='width:70%;'>
    <tr><td>Words: </td><td>$user_total_words</td></tr>
    <tr><td>Pageviews: </td><td><span id='hrefjsontoadd'>$user_total_views</span></td></tr>
    </table>";
    //---
	echo "
	<div class='row content'>
		<div class='col-md-4'>$table2</div>
		<div class='col-md-4'><h2 class='text-center'>$man</h2></div>
		<div class='col-md-4'></div>
	</div>";
	//---
	return  $sato;
    // print "</div>";
    //---
};
//---
function make_pend() {
    global $dd_Pending, $Words_table;
    $sato_Pending ='
	<table class="table table-striped compact soro">
		<thead>
            <tr>
                <th>#</th>
                <th>Lang.</th>
                <th>Title</th>
                <th>Category</th>
                <th>Words</th>
                <th>type</th>
                <th>Translated</th>
                <th>Start date</th>
			</tr>
		</thead>
		<tbody>
    ';
    //---
    $bnd = '';
    $bnd .= $sato_Pending;
    //---
    $dff = 0;
    foreach ( $dd_Pending AS $title=> $kk ) {
        //---
        $dff      = $dff + 1;
        //---
        $lange    = $kk['lang'];
        // $lang2    = isset($code_to_lang[$lange]) ? $code_to_lang[$lange] : $lange;
        // $lang2    = isset($lang_code_to_en[$lange]) ? $lang_code_to_en[$lange] : $lange;
        // $lang2    = isset($lang2) ? $lang2 : $lange;
        //---
        // $tran_type = $kk['translate_type'];
        $tran_type = isset($kk['translate_type']) ? $kk['translate_type'] : '';
        if ($tran_type == 'all') { 
            $tran_type = 'Whole article';
        };
        //---
        $md_title = $kk['title'];
        $word     = $kk['word'];
        //---
        $worde = isset($word) ? $word : $Words_table[$md_title];
        $nana = make_mdwiki_title( $md_title );
        $bnd .= '
            <tr>
                <td>' . $dff   . '</td>
                <td><a target="" href="langs.php?langcode=' . $lange . '">' . $lange . '</a>' . '</td>
                <td>' . $nana  . '</td>
                <td>' . make_cat_url( $kk['cat'] )  . '</td>
                <td>' . $worde . '</td>
                <td>' . $tran_type . '</td>
                <td>Pending</td>
                <td>' . $kk['date']  . '</td>
    ';
        //---
        $bnd .= '</tr>';
        //---
    };
    //---
    $bnd .= '
		</tbody>
    </table>';
    //---
    return $bnd;
}
//---
$test = isset($_GET['test']) ? $_GET['test'] : '';
$mainuser = isset($_GET['user']) ? $_GET['user'] : '';
$limit = isset($_GET['limit']) ? $_GET['limit'] : null;
//---
if ($mainuser != '') {
    $sas = make_user_table($mainuser, $test, $limit);
	//---
    print "
	<div class='card'>
		<div class='card-body' style='padding:5px 0px 5px 5px;'>
		$sas
		</div>
	</div>";
	//---
	$bnd = make_pend();
	//---
    print "
    <br>
	<div class='card'>
		<div class='card-body' style='padding:5px 0px 5px 5px;'>
        <h2 class='text-center'>Translations in process</h2>
		$bnd
		</div>
	</div>";
    //---

	//---
} else {
    print "<h2 class='text-center'>no user name..</h2>";
};
//---
require('foter.php');
//---
if ($_GET['test'] != '') print_mem();
//---
?>