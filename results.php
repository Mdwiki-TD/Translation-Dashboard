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
// $depth  = isset($_REQUEST['depth']) ? $_REQUEST['depth'] : 1;
// $depth  = $depth * 1 ;
//---
$cat = isset($_REQUEST['cat']) ? $_REQUEST['cat'] : '';
//---
if ($cat == "undefined") { $cat = "RTT";};
//---
function make_table( $items, $cod, $cat ) {
    global $username, $Words_table, $All_Words_table, $Assessments_table, $Assessments_fff ,$Translate_type;
    global $Lead_Refs_table, $All_Refs_table, $enwiki_pageviews_table;
    global $qids_table;
    //---
    //$frist = '<table class="table table-sm table-striped" id="main_table">';
    //$frist = "<caption>$res_line</caption>";
    //--- 
    $Refs_word = 'Lead refs';
    //---
    $Words_word = 'Words';
    if ($Translate_type == 'all') { 
        $Words_word = 'words';
        $Refs_word = 'References';
        };
    //---
	$qidth = '';
	//---
	if ($username == 'Mr. Ibrahem') $qidth = "<th>qid</th>";
	//---
    $frist = '
    <table class="table table-sm sortable table-striped" id="main_table">
    <thead>
        <tr>
        <th class="num">#</th>
        <th class="spannowrap" tt="h_title">Title</th>
        <th class="spannowrap" tt="h_len">Translate</th>
        <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="Page views in last month in English Wikipedia">Pageviews</span>
 </th>
        <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="Page important from medicine project in English Wikipedia">Importance</span></th>
        <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="number of word of the article in mdwiki.org">' . $Words_word . '</span></th>
        <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="number of reference of the article in mdwiki.org">' . $Refs_word . '</span></th>
		' . $qidth . '
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
    //---
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
            //---
			$qid = isset($qids_table[$title]) ? $qids_table[$title] : "";
			$qid = isset($qid) ? "<a href='https://wikidata.org/wiki/$qid'>$qid</a>" : '';
			//---
			$qidline = '';
            //---
			if ($username == 'Mr. Ibrahem') $qidline = "<td>$qid</td>";
            //---
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
                $tab = "<a href='" . $translate_url . "' class='btn btn-primary btn-sm'>Translate</a>";
            } else {
                // $tab = "<a class='btn btn-danger btn-sm' href='login5.php?action=login'><span class='glyphicon glyphicon-log-in'></span> Login</a>";
                $tab = "<a role='button' class='btn btn-primary' onclick='login()'>
							<i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
						  </a>";
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
		$qidline
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
if ( $code_lang_name != '' ) $doit2 = true;
//---
echo "<div class='container'>";
//---
if ( $doit && $doit2 ) {
    //---
    if ($test) print '$doit and $doit2:<br>';
    //---
    $items = array() ;
    //---
    $items = get_cat_members( $cat, $depth, $code, $test ) ; # mdwiki pages in the cat
    //---
    $len_of_exists_pages = $items['len_of_exists'];
    $items_missing       = $items['missing'];
    //---
    $missing = array();
    foreach ( $items_missing as $key => $cca ) if (!in_array($cca,$missing)) $missing[] = $cca;
    //---
    $len_of_missing_pages = count($missing);
    $len_of_all           = $len_of_exists_pages + $len_of_missing_pages;
    //---
	$cat2 = "Category:" . str_replace ( 'Category:' , '' , $cat );
	$caturl = "<a href='https://mdwiki.org/wiki/$cat2'>category</a>";
    //---
    $ix =  "Find $len_of_all pages in $caturl, $len_of_exists_pages exists, and $len_of_missing_pages missing in (<a href='https://$code.wikipedia.org'>https://$code.wikipedia.org</a>)." ;
    //---
    $res_line = " Results " ;//. ($start+1) . "&ndash;" . ($start+$limit) ;
    //---
    if ($test != '') $res_line .= 'test:';
    //---
    $table = make_table( $missing, $code, $cat ) ;
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
    if (isset($doit) && $test != '' ) {
        //---
        print "_REQUEST code:" . isset($_REQUEST['code']) . "<br>";
        print "code:$code<br>";
        print "code_lang_name:$code_lang_name<br>";
        //---
    };
    echo '</div>';
};
//---
echo "</div>";
//---
?>
