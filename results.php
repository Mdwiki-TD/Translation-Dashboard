<?PHP
//---
include_once('tables.php'); 
include_once('langcode.php');
include_once('getcats.php');
include_once('functions.php');
include_once('sql_tables.php');
//---
$doit = isset($_REQUEST['doit']);
$test = $_REQUEST['test'] ?? '';
//---
$code = $_REQUEST['code'] ?? '';
//---
if ($code == 'undefined') $code = "";
//---
$code = isset($lang_to_code[$code]) ? $lang_to_code[$code] : $code;
$code_lang_name = isset($code_to_lang[$code]) ? $code_to_lang[$code] : ''; 
//---
$tra_type  = $_REQUEST['type'] ?? '';
//---
$cat = $_REQUEST['cat'] ?? '';
//---
if ($cat == "undefined") $cat = "RTT";
//---
function sort_py_PageViews( $items ) {
    //---
    global $enwiki_pageviews_table;
    //---
    $dd = array();
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
    return $dd;
};
//---
function sort_py_importance( $items ) {
    //---
    global $Assessments_fff, $Assessments_table;
    $dd = array();
    //---
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
    };
    //---
    arsort($dd);
    //---
    return $dd;
};
//---
function make_table( $items, $cod, $cat, $inprocess=false ) {
    global $username, $Words_table, $All_Words_table, $Assessments_table ,$tra_type;
    global $Lead_Refs_table, $All_Refs_table, $enwiki_pageviews_table;
    //---
    global $sql_qids;
    //---
    $Refs_word = 'Lead refs';
    $Words_word = 'Words';
    //---
    if ($tra_type == 'all') { 
        $Words_word = 'words';
        $Refs_word = 'References';
        };
    //---
    $Translate_th = '<th class="spannowrap" tt="h_len">Translate</th>';
    //---
    $in_process = array();
    $inprocess_first = '';
    //---
    if ( $inprocess ) {
        $inprocess_first = '<th>user</th><th>date</th>';
        //---
        $in_process = $items;
        //---
        $items = array_keys($items);
        //---
        $Translate_th = '';
    };
    //---
    $frist = '
        <table class="table table-sm sortable table-striped" id="main_table">
            <thead>
                <tr>
                    <th class="num">#</th>
                    <th class="spannowrap" tt="h_title">Title</th>
                    ' . $Translate_th . '
                    <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="Page views in last month in English Wikipedia">Pageviews</span>
                    </th>
                    <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="Page important from medicine project in English Wikipedia">Importance</span></th>
                    <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="number of word of the article in mdwiki.org">' . $Words_word . '</span></th>
                    <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="number of reference of the article in mdwiki.org">' . $Refs_word . '</span></th>
                    <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="Wikidata identifier">qid</span></th>
                    ' . $inprocess_first . '
                </tr>
            </thead>
            <tbody>' ;
    //---
    $dd = array();
    //---
    // $dd = sort_py_importance($items);
    $dd = sort_py_PageViews($items);
    //---
    $list = "" ;
    $cnt = 1 ;
    //---
    foreach ( $dd AS $v => $gt) {
        if ( $v == '' ) continue;
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
        $qid = isset($sql_qids[$title]) ? $sql_qids[$title] : "";
        $qid = ($qid != '') ? "<a href='https://wikidata.org/wiki/$qid'>$qid</a>" : '';
        //---
        $word = isset($Words_table[$title]) ? $Words_table[$title] : 0; 
        //---
        $refs = isset($Lead_Refs_table[$title]) ? $Lead_Refs_table[$title] : 0; 
        //---
        if ($tra_type == 'all') { 
            $word = isset($All_Words_table[$title]) ? $All_Words_table[$title] : 0;
            $refs = isset($All_Refs_table[$title]) ? $All_Refs_table[$title] : 0;
            };
        //---
        $asse = isset($Assessments_table[$title]) ? $Assessments_table[$title] : '';
        //---
        if ( $asse == '' ) $asse = 'Unknown';
        //---
        $params = array(
            "title" => $title2,
            "code" => $cod,
            "username" => $username,
            "cat" => $cat2,
            "type" => $tra_type
            );
        //---
        $translate_url = 'translate.php?' . http_build_query($params);
        //---
        $tab = "
            <a role='button' class='btn btn-primary' onclick='login()'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>";
        //---
        if ( $username != '' ) $tab = "<a href='$translate_url' class='btn btn-primary btn-sm'>Translate</a>";
        //---
        $tab_td = "<td class='num'>$tab</td>";
        //---
        $inprocess_tds = '';
        if ( $inprocess ) {
            $_user_ = $in_process[$v]['user'];
            $_date_ = $in_process[$v]['date'];
            $inprocess_tds = "<td>$_user_</td><td>$_date_</td>";
            $tab_td = '';
        };
        //---
        $list .= "
            <tr>
                <td class='num'>$cnt</td>
                <td class='link_container spannowrap'><a target='_blank' href='$urle'>$title</a></td>
                $tab_td
                <td class='num'>$pageviews</td>
                <td class='num'>$asse</td>
                <td class='num'>$word</td>
                <td class='num'>$refs</td>
                <td>$qid</td>
                $inprocess_tds
            </tr>" ;
        //---
        $cnt++ ;
        //---
    };
    //---
    $script = '' ;
    if ($script =='3') $script = '';
    //---
    $last = "</tbody></table>";
    //---
    return $frist . $list . $last . $script ;
    //---
    }
//---
function get_in_process($missing, $code) {
    $qua = "select * from pages where target = '' and lang = '$code';";
    //---
    $res = execute_query($qua);
    //---
    // echo "<br>";
    // var_export(json_encode($res));
    //--
    $titles = array();
    //---
    foreach ( $res AS $t) {
        if (in_array($t['title'], $missing)) $titles[$t['title']] = $t;
    }
    //---
    // var_export(json_encode($titles));
    //--
    return $titles;
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
    if ($test) echo '$doit and $doit2:<br>';
    //---
    $items = array() ;
    //---
    $items = get_cat_members($cat, $depth, $code, $test) ; # mdwiki pages in the cat
    //---
    $len_of_exists_pages = $items['len_of_exists'];
    $items_missing       = $items['missing'];
    //---
    $missing = array();
    foreach ( $items_missing as $key => $cca ) if (!in_array($cca, $missing)) $missing[] = $cca;
    //---
    $in_process = get_in_process($missing, $code);
    //---
    $len_in_process = count($in_process);
    //---
    $len_of_missing_pages = count($missing);
    $len_of_all           = $len_of_exists_pages + $len_of_missing_pages;
    //---
	$cat2 = "Category:" . str_replace ( 'Category:' , '' , $cat );
	$caturl = "<a href='https://mdwiki.org/wiki/$cat2'>category</a>";
    //---
    $ix =  "Find $len_of_all pages in $caturl, $len_of_exists_pages exists, and $len_of_missing_pages missing in (<a href='https://$code.wikipedia.org'>https://$code.wikipedia.org</a>), $len_in_process In process." ;
    //---
    $res_line = " Results " ;//. ($start+1) . "&ndash;" . ($start+$limit) ;
    //---
    if ($test != '') $res_line .= 'test:';
    //---
    // delete $in_process keys from $missing
    if ($len_in_process > 0) {
        $missing = array_diff($missing, array_keys($in_process));
    };
    //---
    $table = make_table($missing, $code, $cat) ;
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
    if ($len_in_process > 0) {
        //---
        $table_2 = make_table($in_process, $code, $cat, $inprocess=true) ;
        //---
        echo "
        <br>
        <div class='card'>
            <div class='card-header'>
                <h5>$len_in_process in process</h5>
            </div>
            <div class='card-body'>
                $table_2
            </div>
        </div>";
    };
    //---
    if (isset($doit) && $test != '' ) {
        //---
        echo "_REQUEST code:" . isset($_REQUEST['code']) . "<br>";
        echo "code:$code<br>";
        echo "code_lang_name:$code_lang_name<br>";
        //---
    };
    echo '</div>';
};
//---
echo "</div>";
//---
?>
