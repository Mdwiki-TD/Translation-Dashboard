<?PHP
//---
require('tables.php'); 
require('langcode.php');
require('getcats.php');
include_once('functions.php');
//---
$doit = isset($_REQUEST['doit']);
$test = isset($_REQUEST['test']) ? $_REQUEST['test'] : '';
//---
$code = isset($_REQUEST['code']) ? $_REQUEST['code'] : '';
//---
if ($code == 'undefined') { $code = ""; };
//---
$code = isset($lang_to_code[$code]) ? $lang_to_code[$code] : $code;
$code_lang_name = isset($code_to_lang[$code]) ? $code_to_lang[$code] : ''; 
//---
$Translate_type  = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$depth  = isset($_REQUEST['depth']) ? $_REQUEST['depth'] : 1;
$depth  = $depth * 1 ;
//---
$cat = isset($_REQUEST['cat']) ? $_REQUEST['cat'] : '';
//---
if ($cat == "undefined") { $cat = "RTT";};
//---
$cat_ch = htmlspecialchars($cat, ENT_QUOTES);
//---
function make_table( $items, $cod, $cat ) {
    global $username, $Words_table, $All_Words_table, $Assessments_table, $Assessments_fff ,$Translate_type;
    global $Lead_Refs_table, $All_Refs_table, $enwiki_pageviews_table;
    
    //$frist = '<table class="table table-sm table-striped" id="main_table">';
    //$frist = "<caption>$res_line</caption>";
    //=== 
    $Refs_word = 'Lead refs';
    //===
    $Words_word = 'Words';
    if ($Translate_type == 'all') { 
        $Words_word = 'words';
        $Refs_word = 'References';
        };
    //===
    $frist = '
    <table class="table sortable table-striped" id="main_table">
    <thead>
        <tr>
        <th onclick="sortTable(0)" class="num">#</th>
        <th onclick="sortTable(1)" class="spannowrap" tt="h_title">Title</th>
        <th class="spannowrap" tt="h_len">Translate</th>
        <th class="spannowrap" tt="h_len">Pageviews</th>
        <th onclick="sortTable(2)" class="spannowrap" tt="h_len">Importance</th>
        <th onclick="sortTable(3)" class="spannowrap" tt="h_len">' . $Words_word . '</th>
        <th onclick="sortTable(3)" class="spannowrap" tt="h_len">' . $Refs_word . '</th>
        </tr>
    </thead>
    <tbody>
    ' ;
    //---
	
    //---
    $dd = array();
    //---
    // sort py Importance
    /*
    foreach ( $items AS $t ) {
        $t = str_replace ( '_' , ' ' , $t );
        //---
        $aa = isset($Assessments_table[$t]) ? $Assessments_table[$t] : null;
        //---
        $kry = isset($Assessments_fff['Unknown']) ? $Assessments_fff['Unknown'] : '';
        //---
        if ( isset($aa) ) {
            $kry = isset($Assessments_fff[$aa]) ? $Assessments_fff[$aa] : $Assessments_fff['Unknown'];
        };
        //---
        $dd[$t] = $kry;
        //---
    };*/
    //---
    // sort py PageViews
    foreach ( $items AS $t ) {
        $t = str_replace ( '_' , ' ' , $t );
        //---
        $kry = isset($enwiki_pageviews_table[$t]) ? $enwiki_pageviews_table[$t] : 0; 
        //---
        $dd[$t] = $kry;
        //---
    };
    //---
    arsort($dd);
    //---
    $list = "" ;
    $cnt = 1 ;
    //print $username;
    // foreach ( $items AS $v ) {
    foreach ( $dd AS $v => $gt) {
        if ( $v != '' ) {
            $title = str_replace ( '_' , ' ' , $v );
            //---
            $title2 = rawurlEncode($title);
            //---
            $cat2 = rawurlEncode($cat);
            //---
            $urle = "//mdwiki.org/wiki/$title2";
            $urle = str_replace( '+' , '_' , $urle );
            //---
            $pageviews = isset($enwiki_pageviews_table[$title]) ? $enwiki_pageviews_table[$title] : 0; 
            // };
            $word = isset($Words_table[$title]) ? $Words_table[$title] : 0; 
            //---
            $refs = isset($Lead_Refs_table[$title]) ? $Lead_Refs_table[$title] : 0; 
            //---
            if ($Translate_type == 'all') { 
                $word = isset($All_Words_table[$title]) ? $All_Words_table[$title] : 0;
                $refs = isset($All_Refs_table[$title]) ? $All_Refs_table[$title] : 0;
                };
            //---
            $asse = isset($Assessments_table[$title]) ? $Assessments_table[$title] : '';
            //---
            if ( $asse == '' ) { $asse = 'Unknown'; }; 
            //---
            $params = array(
                "title" => $title2,
                "code" => $cod,
                "username" => $username,
                "cat" => $cat2,
                "type" => $Translate_type
                );
            $translate_url = 'translate.php?' . http_build_query($params);
            //---
            if ( $username != '' ) {
                $tab = "<a href='" . $translate_url . "' class='btn btn-primary'>Translate</a>";
            } else {
                $tab = "<a class='btn btn-danger' href='login5.php?action=login'><span class='glyphicon glyphicon-log-in'></span> Login</a>";
            };
            //---
            $list .= "
        <tr>
        <td class='num'>$cnt</td>
        <td class='link_container spannowrap'><a target='_blank' href='$urle'>$title</a></td>
        <td class='num'>$tab</td>
        <td class='num'>$pageviews</td>
        <td class='num'>$asse</td>
        <td class='num'>$word</td>
        <td class='num'>$refs</td>
        </tr>   
    " ;
            //---
            $cnt++ ;
            //---
        }
    }
    //---
    $script = '' ;
    if ($script =='3') {  $script = ''; };
    //---
    $last = "</tbody></table>
    " ;
    return $frist . $list . $last . $script ;
    //---
    }
//---
$doit2 = false ;
//---
if ( $code_lang_name != '' ) { $doit2 = true ; };
//---
if ( $doit && $doit2 ) {
    //---
    if ($test) {
        print '$doit and $doit2:<br>';
    };
    //---
    $items = array() ;
    //---
    // if ($test == 'test') {
    //---
    $items = get_cat_members_with_php( $cat, $depth, $code, $test ) ; # mdwiki pages in the cat
    // } else {
        // $items = get_cat_members_with_py( $cat , $depth , $code , $test ) ; # mdwiki pages in the cat
    // };
    //---
    $len_of_all = isset($items['len_of_all']) ? $items['len_of_all'] : 0 ;
    
    $len_of_exists_pages = isset($items['len_of_exists']) ? $items['len_of_exists'] : 0 ;
    
    $len_of_missing_pages = isset($items['len_of_missing']) ? $items['len_of_missing'] : 0 ;
    //---
    $res_line = " Results " ;//. ($start+1) . "&ndash;" . ($start+$limit) ;
    //---
    if ($test != '') { 
        $res_line .= 'test:';
    };
    //---
    $items_missing = isset($items['missing']) ? $items['missing'] : array();
    $table = make_table( $items_missing, $code , $cat ) ;
    //---
    $ix =  "Find $len_of_all pages in categories, $len_of_exists_pages exists, and $len_of_missing_pages missing in (<a href='https://$code.wikipedia.org'>https://$code.wikipedia.org</a>)." ;
    //---
    $diff = isset($items['diff']) ? $items['diff'] : '';
    if ($diff != '' ) {
        $ix .= " diff:($diff)";
    };
    //---
    echo "
	<br>
  <div class='card'>
    <h4>$res_line:</h4>
    <div class='card-header'>
      <h5>$ix</h5>
      </div>
      <div class='card-body'>
      $table
    </div>
  </div>";
    //---
    $misso  = isset($items['misso']) ? $items['misso'] : array();
    if ($misso != '') {
        $table3 = make_table( $misso, $code , $cat ) ;
        print "<div class='card'>
    <div class='card-header'>
         <h4>pages exists in mdwiki and missing in enwiki:</h4>
    </div>
    <div class='card-body' style='padding:5px 0px 5px 5px;'>
  $table3
  </div>
  </div>
  " ;
    };
    //---
    if ($test != '') {
        $enwiki_missing = isset($items['enwiki_missing']) ? $items['enwiki_missing'] : array();
        $table2 = make_table( $enwiki_missing , $code , $cat ) ;
        print $table2 ;
    };
    //---
} else {
    if (isset($doit) && $test != '' ) {
        //===
        print "_REQUEST code:" . isset($_REQUEST['code']) . "<br>";
        print "code:$code<br>";
        print "code_lang_name:$code_lang_name<br>";
        //===
    };
    echo '</div>';
};
//---
?>