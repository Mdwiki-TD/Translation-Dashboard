<?PHP
//---
require('header.php');
//---
print '</div>

<div style="margin-right:25px;margin-left:25px;boxSizing:border-box;" align=left >';
//---
if ($_GET['test'] != '') {
    echo(__file__);
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
//---
function make_td_fo_user($tabg, $nnnn) {
    // ------------------
    global $code_to_lang, $Words_table, $user_views_sql, $user_total_words;
    // ------------------
    $date     = $tabg['date'];
    //---
    //return $date . '<br>';
    //---
    $llang    = $tabg['lang'];
    $md_title = $tabg['title'];
    $cat      = $tabg['cat'];
    $targe    = $tabg['target'];
    $pupdate  = isset($tabg['pupdate']) ? $tabg['pupdate'] : '';
    // ------------------
    $views_number = isset($user_views_sql[$targe]) ? $user_views_sql[$targe] : '?';
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
            <td>' . $pupdate . '</td>
            <td>' . $view . '</td>
        </tr>
        '; 
    //---
    return $laly;
};
//===
function make_user_table($user_main, $test, $limit) {
    //---
    global $code_to_lang, $Words_table, $user_views_sql, $user_total_words;
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
    $user_total_views = 0;
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
        if (sizeof($views_query) == 0) { $done = $user_count;};
        //---
        foreach ( $views_query AS $Key => $table ) {
            $countall = $table['countall'];
            $targ = $table['target'];
            $user_views_sql[$targ] = $countall;
            //---
            $user_total_views = $user_total_views + $countall;
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
    //===
    $sato = '  
    <table id="myTable" class="table display"> <!-- scrollbody -->
        <thead>
            <tr>
                <th onclick="sortTable(0)">#</th>
                <th onclick="sortTable(1)">Language</th>
                <th onclick="sortTable(2)">Title</th>
                <!--<th onclick="sortTable(3)">Start date</th> -->
                <th onclick="sortTable(4)">Category</th>
                <th onclick="sortTable(5)">Words</th>
                <th onclick="sortTable(6)">type</th>
                <th onclick="sortTable(7)">Translated</th>
                <th onclick="sortTable(8)">Completion date</th>
                <th onclick="sortTable(9)">Pageviews</th>
    ';
    //---
    if ($test != '') $sato .= '
                <th onclick="sortTable(10)">User</th>';
    //===
    $sato .= '
            </tr>
        </thead>
        <tbody>';
    //---
    $quaa = "select * from pages where user = '$user_main'";
    //---
    if ($limit != '' && is_numeric($limit)) $quaa = $quaa . " limit $limit";
    //---
    if ($test != '') echo $quaa;
    //---
    $sql_result = quary2($quaa);
    //---
    $dd_Pending = array();
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
    //===
    $table2 = "<table class='table table-striped' style='width:70%;'>
    <tr><td>Words: </td><td>$user_total_words</td></tr>
    <tr><td>Pageviews: </td><td>$user_total_views</td></tr>
    </table>";
    //=== 
    echo "<table border=0 style='width:100%;'>
    <tr>
        <td style='width:33%;'>$table2</td>
        <td style='width:33%;'><h2 class='text-center'>$man</h2></td>
        <td style='width:33%;'></td>
    </tr>
    </table>
    ";
    //---
    // print '<div class=" clearfix leaderboard" >';
    //---
    // require("filter-table.php");
    //---
    print "<div class='card'>
  <div class='card-body' style='padding:5px 0px 5px 5px;'>
  $sato
  </div>
  </div>"
  ;
    // print "</div>";
    //---
    print '
    <div class="text-center clearfix leaderboard" >
        <h2 class="text-center">Translations in process</h2>
    ';
    //===
    //---
    $sato_Pending ='  <table class="sortable table table-striped alignleft"> <!-- scrollbody -->
            <tr>
                <th onclick="sortTable(0)">#</th>
                <th onclick="sortTable(1)">Language</th>
                <th onclick="sortTable(2)">Title</th>
                <th onclick="sortTable(3)">Category</th>
                <th onclick="sortTable(4)">Words</th>
                <th onclick="sortTable(5)">type</th>
                <th onclick="sortTable(6)">Translated</th>
                <th onclick="sortTable(7)">Start date</th>
    ';
    //---
    $sato_Pending .= '
        </tr>';
    //---
    print $sato_Pending;
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
        print '
            <tr>
                <td>' . $dff   . '</td>
                <td><a target="" href="langs.php?langcode=' . $lange . '">' . $lang2 . '</a>' . '</td>
                <td>' . $nana  . '</td>
                <td>' . make_cat_url( $kk['cat'] )  . '</td>
                <td>' . $worde . '</td>
                <td>' . $tran_type . '</td>
                <td>Pending</td>
                <td>' . $kk['date']  . '</td>
    ';
        //---
        print '</tr>';
        //---
    };
    //---
    print '
    </table>';
    print "
    </div>
    <div>
    ";
    //---
};
//---
$test = isset($_GET['test']) ? $_GET['test'] : '';
$mainuser = isset($_GET['user']) ? $_GET['user'] : '';
$limit = isset($_GET['limit']) ? $_GET['limit'] : null;
//---
if ($mainuser != '') {
    make_user_table($mainuser, $test, $limit);
} else {
    print "<h2 class='text-center'>no user name..</h2>";
};
//---
echo "<script>
$(document).ready( function () {
    $('#myTable').DataTable();
} );
</script>";
//---
require('foter.php');
print "</div>";
//---
if ($_GET['test'] != '') print_mem();
//---

?>