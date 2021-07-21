<?PHP
//-----------------
require ('header1.php');
require ('langcode.php');
require ('tables.php');
require ('functions.php');
//-----------------
$test = $_GET['test'];
$mainuser = $_GET['user'];
$mainuser = rawurldecode( str_replace ( '_' , ' ' , $mainuser ) );
//-----------------
$man = make_mdwiki_user_url($mainuser);
print '<div class="col-md-10 col-md-offset-1" align=left >';
print "
<h1 class='text-center'>$man</h1>
";
print '<div class="text-center clearfix leaderboard" >
';
//=============================
$table_leg = '  <table class="sortable table table-striped alignleft">
        <tr>
            <th onclick="sortTable(0)">#</th>
            <th onclick="sortTable(1)">Language</th>
            <th onclick="sortTable(2)">Title</th>
            <!--<th onclick="sortTable(3)">Start date</th> -->
            <th onclick="sortTable(4)">Category</th>
            <th onclick="sortTable(5)">Words</th>
            <th onclick="sortTable(6)">Translated</th>
';
#----------------- 
//=============================
//------------------------------
$vieww = $views_table->{'byuser'};
$vviews = $vieww->{$mainuser};
//------------------------------
$vas = 'Pageviews';
if ( $vviews != '') { $vas .= "<br>($vviews)"; };
//------------------------------
$sato = $table_leg . "
            <th onclick='sortTable(7)'>Completion date</th><th onclick='sortTable(6)'>$vas</th>";
//=============================
if ($test != '') $sato .= '
            <th onclick="sortTable(8)">User</th>';
//=============================
$sato .= '
        </tr>';
//=============================
# { 'user' : useradd , 'lang' : code , 'title' : title , 'word' : Words.get(title,0) , 'date' : menet }
#----------------- 
//-----------------
function make_td($tat,$tabg,$nnnn) {
    // ------------------
    global $code_to_lang, $Words_table;
    // ------------------
    $date     = $tabg['date'];
    //------------------------------------
    //return $date . '<br>';
    //------------------------------------
    $llang    = $tabg['lang'];
    $md_title = $tabg['title'];
    $cat      = $tabg['cat'];
    $word     = $tabg['word'];
    $targe    = $tabg['target'];
    $pupdate  = isset($tabg['pupdate']) ? $tabg['pupdate'] : '';
    // ------------------
    $lang2 = isset($code_to_lang[$llang]) ? $code_to_lang[$llang] : $llang;
    $md_title = isset($md_title) ? $md_title : '';
    //------------------------------------
    $ccat = make_cat_url( $cat );
    //------------------------------------
    $worde = isset($word) ? $word : $Words_table->{$md_title};
    //------------------------------------
    $nana = make_mdwiki_title( $md_title );
    //------------------------------------
    $targe33 = make_target_url( $targe , $llang );
    //------------------------------------
    $view = make_view($targe , $llang) ;
    //------------------------------------
    $laly = '
        <tr>
            <td>' . $nnnn   . '</td>
            <td><a target="" href="langs.php?langcode=' . $llang . '">' . $lang2 . '</a>' . '</td>
            <td>' . $nana  . '</td>
            <!-- <td>' . $date  . '</td> -->
            <td>' . $ccat  . '</td>
            <td>' . $worde . '</td>
            <td>' . $targe33 . '</td>
            <td>' . $pupdate . '</td>
            <td>' . $view . '</td>
            '; 
    //-----------------------
    $laly .= '
        </tr>';
    //------------------------------------
    return $laly;
};
//-----------------
//==========================
$quaa = "select * from pages where user = '$mainuser'";
// print($quaa);
//-----------------
if ($_SERVER['SERVER_NAME'] == 'mdwiki.toolforge.org') { 
    $sql_u = quary($quaa);
} else {
    $datna = array('code'  => $quaa,'raw' => 't');
    $postdata = http_build_query($datna);
    //-------------
    $opts = array('http' =>
        array( 
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $url ='//mdwiki.toolforge.org/Translation_Dashboard/sql.php';
    // $contextv = stream_context_create($opts);
    // $sql_u = file_get_contents($url, false, $contextv);
    //-------------
    $url .= '?' . http_build_query($datna, '', '&');
    $sql_u = file_get_contents($url);
    $sql_u = json_decode($sql_u);
    //-------------
    print($url);
    print($sql_u);
    //-------------
};
//-----------------
$sql_result = array();
//-----------------
$n = 0;
//-----------------
foreach ( $sql_u AS $id => $row ) {
    $ff = array();
    $n = $n + 1 ;
    foreach ( $row AS $nas => $value ) {
        $ff[$nas] = $value;
    };
    $sql_result[$n] = $ff;
};
//-----------------
// print(var_dump($sql_result));
//-----------------
$dd_Pending = array();
$dd = array();
// foreach ( $sql_result AS $lkl => $gg ) {
foreach ( $sql_result AS $tait => $tabg ) {
        #------------
        // $kry = str_replace('-','',$tabg['pupdate']) . ':' . $tabg['target'] ;
        $kry = str_replace('-','',$tabg['pupdate']) . ':' . $tabg['lang'] . ':' . $tabg['title'] ;
        //print $kry . '<br>';
        #------------
        if ( $tabg['target'] != '' ) {
            $dd[$kry] = $tabg;
        } else {
            $dd_Pending[$kry] = $tabg;
        };
        #------------
    };
// };
//-----------------
krsort($dd);
// print( count($dd) );
//-----------------
$noo = 0;
foreach ( $dd AS $tat => $tabe ) {
    //------------------------------------
    $noo = $noo + 1;
    //------------------------------------
    $sato .= make_td($tat,$tabe,$noo);
    //------------------------------------
};
#-----------------
$sato .= '
    </table>';
print $sato;
print "
</div>";
#-----------------
print '
<div class="text-center clearfix leaderboard" >
    <h1 class="text-center">Translations in process</h1>
';
//==========================
//-----------------
$sato_Pending ='  <table class="sortable table table-striped alignleft">
        <tr>
            <th onclick="sortTable(0)">#</th>
            <th onclick="sortTable(1)">Language</th>
            <th onclick="sortTable(2)">Title</th>
            <th onclick="sortTable(3)">Start date</th>
            <th onclick="sortTable(4)">Category</th>
            <th onclick="sortTable(5)">Words</th>
            <th onclick="sortTable(6)">Translated</th>';
//-----------------
if ($username == $mainuser) { 
    $sato_Pending .= '<th onclick="sortTable(7)">Completion</th>'; 
};
//-----------------
$sato_Pending .= '
    </tr>';
//-----------------
print $sato_Pending;
//-----------------
$dff = 0;
foreach ( $dd_Pending AS $title=> $kk ) {
    //------------------------------------
    $dff      = $dff + 1;
    //------------------------------------
    $lange    = $kk['lang'];
    $lang2    = isset($code_to_lang[$lange]) ? $code_to_lang[$lange] : $lange;
    //------------------------------------
    $md_title = $kk['title'];
    $word     = $kk['word'];
    $lang2    = isset($lang2) ? $lang2 : $lange;
    //------------------------------------
    $worde = isset($word) ? $word : $Words_table->{$md_title};
    $nana = make_mdwiki_title( $md_title );
    print '
        <tr>
            <td>' . $dff   . '</td>
            <td><a target="" href="langs.php?langcode=' . $lange . '">' . $lang2 . '</a>' . '</td>
            <td>' . $nana  . '</td>
            <td>' . $kk['date']  . '</td>
            <td>' . make_cat_url( $kk['cat'] )  . '</td>
            <td>' . $worde . '</td>
            <td>Pending</td>';
    //------------------------------------
    if ($username == $mainuser) { 
        $md_title2 = rawurlencode(str_replace ( ' ' , '_' , $md_title ) );
        $urle = "//$lange.wikipedia.org/wiki/Special:ContentTranslation?page=User%3AMr._Ibrahem%2F$md_title2&from=en&to=$lange";
        print "<td><a href='$urle'>Completion</a></td>"; 
    };
    //------------------------------------
    print '</tr>';
    //------------------------------------
};
//-----------------
print '
   </table>';
print "
</div>
";
//-----------------
//-----------------
require ('foter1.php');
print "</div>";
//-----------------

?>