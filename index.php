<?PHP
//--------------------
require('header.php');
require('tables.php');
require('langcode.php');
// include_once('functions.php');
require('functions.php');
//--------------------
$python3 = 'python3';
$projects_dirr = '/mnt/nfs/labstore-secondary-tools-project';
//--------------------
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
    $projects_dirr = '/master';
    $python3 = 'python';
};
//--------------------
$doit = isset($_REQUEST['doit']);
$test = $_REQUEST['test'];
//--------------------
$prefilled_requests = [] ;
//--------------------
$code = $_REQUEST['code'];
$code = $lang_to_code[$code] != '' ? $lang_to_code[$code] : $code;
$code_lang_name = $code_to_lang[$code]; 
//--------------------
$Translate_type  = $_REQUEST['type'];
$depth  = $_REQUEST['depth'] * 1 ;
$cat    = $_REQUEST['cat'];
$cat_ch = htmlspecialchars($cat,ENT_QUOTES);
//--------------------
function test_print($s) {
    global $test;
    if ($test != '') { print $s; };
};
//--------------------
function print_index_start() {
    return '
<!-- <div class="col-md-10 col-md-offset-1" align=left > -->
<div style="margin-right:8%;margin-left:8%;boxSizing:border-box;" align=left >
<div style="float:right">
<img src="//upload.wikimedia.org/wikipedia/commons/thumb/5/58/Wiki_Project_Med_Foundation_logo.svg/400px-Wiki_Project_Med_Foundation_logo.svg.png" decoding="async" width="200" height="200" >
</div>
<p>This tool looks for Wikidata items that have a page on mdwiki.org but not in another wikipedia language <a href="?cat=RTT&depth=1&code=ceb&doit=Do+it"><b>(Example)</b></a>.</p>
<p><a href="//mdwiki.org/wiki/WikiProjectMed:Translation_task_force"><b>How to use.</b></a></p>
' ;
};
//--------------------
function make_datalist() {
    global $lang_to_code,$code_lang_name,$code;
    //--------------------
    $coco = $code_lang_name;
    if ( $coco == '') { $coco = $code ; };
    //--------------------
    $str = '';
    //--------------------
    $str .= "<input size=25 list='Languages' class='span2' type='text' placeholder='two letter code' name='code' id='code' value='$coco'>";
    //--------------------
    $str .= '<datalist id="Languages">';
    //--------------------
    foreach ( $lang_to_code AS $lange => $cod ) {
        $str .= "<option value='$cod'>$lange</option>";
    };
    //--------------------
    $str .= '</datalist></input>' ;
    //--------------------
    return $str;
    //--------------------
};
//--------------------
function make_drop() {
    global $lang_to_code,$code;
    print '<select dir="ltr" id="code" class="form-control custom-select">';
    //--------------------
    foreach ( $lang_to_code AS $lange => $cod ) {
        $cdcdc = $code == $cod ? "selected" : "";
        print "<option id='$cod' $cdcdc>$lange</option>";
    };
    //--------------------
    print '
        </select>
    ' ;
};
//--------------------
function print_form_start() {
    //--------------------
    global $cat_ch,$depth,$code_lang_name,$code , $form_start_done , $Translate_type;
    //--------------------
    $cate = $cat_ch != '' ? $cat_ch : 'RTT' ;
    //--------------------
    $d = '';
    //--------------------
    if ($form_start_done == false ) {
        $d .= "
    <form method='GET' action='index.php' class='form-inline'>
    ";
    };
    //--------------------
    $d .= "
<hr/>
<table class='table-condensed' border=0><tbody>
<tr>
<td><b>Mdwiki category</b></td>
<td>
<input class='span4' type='text' size=25 name='cat' id='cat' value='$cate' placeholder='Root category' /> Depth <input class='span1' name='depth' type='text' size='5' value='$depth' /> (optional)
</td>
</tr>
";
    //--------------------
    $d .= "<tr><td nowrap><b>Target language</b></td>
<td style='width:100%'>" ;
    //--------------------
    $d .= make_datalist();
    // make_drop();
    //--------------------
    if ($code_lang_name == '' and $code != '') { 
        $d .= "<span style='font-size:13pt;color:red'> code ($code) not valid wiki.</span>";
    } else {
        if ($code != '') {
            $_SESSION['code'] = $code;
        };
    };
    // -------------
    $d .= '</td></tr>';
    // -------------
    $lead_checked = "checked";
    $all_checked = "";
    // -------------
    if ($Translate_type == 'all') {
        $lead_checked = "";
        $all_checked = "checked";
    };
    //-------------
    $d .= "
<tr>
    <td>
        <b>Type</b>
    </td>
    <td colspan='2'>
        <input type='radio' name='type' value='lead' $lead_checked> The lead only<br>
        <input type='radio' name='type' value='all' $all_checked> The whole article
    </td>
</tr>
";
    // -------------
    // -------------
    $d .= '<tr><td colspan="3" class="aligncenter">';
    // -------------
    return $d;
    // -------------
};
//--------------------
function print_form_last() {
    return "
</td>
</tr>
</tbody>
</table>
<hr/>
</form>";
};
//--------------------
echo print_index_start();
//--------------------
echo print_form_start();
//--------------------
if ( $username != '' ) {
        echo '<input type="submit" name="doit" class="btn w3-button w3-round-large w3-blue" value="Do it"/>';
    } else {
        echo $input_login_str;
};
echo print_form_last();
//==========================
function make_table( $items , $cod , $cat ) {
    global $username,$Words_table,$All_Words_table,$Assessments_table,$Assessments_fff ,$Translate_type ;
    global $Lead_Refs_table,$All_Refs_table ;
    $frist = '<table class="sortable table table-striped" id="main_table">';
    
    //$frist = '<table class="table table-sm table-striped" id="main_table">';
    //$frist .= "<caption>$res_line</caption>";
    //======================== 
    $Refs_word = 'Lead references';
    //========================
    $Words_word = 'Lead Words';
    if ($Translate_type == 'all') { 
        $Words_word = 'words';
        $Refs_word = 'References';
        };
    //========================
    $frist .= '
<thead><tr>
<th onclick="sortTable(0)" class="num">#</th>
<th onclick="sortTable(1)" class="text-nowrap" tt="h_title">Title</th>
<th onclick="sortTable(2)" class="text-nowrap" tt="h_len">Importance</th>
<th onclick="sortTable(3)" class="text-nowrap" tt="h_len">' . $Words_word . '</th>
<th onclick="sortTable(3)" class="text-nowrap" tt="h_len">' . $Refs_word . '</th>
<th  class="text-nowrap" tt="h_len">Translate</th>
</tr></thead><tbody>
' ;
    //--------------------
    //--------------------
    $dd = array();
    foreach ( $items AS $t ) {
        $t = str_replace ( '_' , ' ' , $t );
        //--------------------
        $aa = $Assessments_table->{$t}; 
        //--------------------
        $kry = $Assessments_fff[$aa] != '' ? $Assessments_fff[$aa] : $Assessments_fff['Unknown'];
        //--------------------
        $dd[$t] = $kry;
        //--------------------
    };
    //--------------------
    asort($dd);
    //--------------------
    //--------------------
    $list = "" ;
    $cnt = 1 ;
    //print $username;
    // foreach ( $items AS $v ) {
    foreach ( $dd AS $v => $gt) {
        if ( $v != '' ) {
            $title = str_replace ( '_' , ' ' , $v );
            $title2 = rawurlEncode($title);
            
            $cat2 = rawurlEncode($cat);
            
            //--------------------
            $urle = "//mdwiki.org/wiki/$title2";
            $urle = str_replace ( '+' , '_' , $urle );
            //--------------------
            $word = $Words_table->{$title}; 
            //--------------------
            $refs = $Lead_Refs_table->{$title}; 
            //--------------------
            if ($Translate_type == 'all') { 
                $word = $All_Words_table->{$title};
                $refs = $All_Refs_table->{$title};
                };
            //--------------------
            $asse = $Assessments_table->{$title}; 
            if ( $asse == '' ) { $asse = 'Unknown'; }; 
            //--------------------
            $params = array(
                "title" => $title2,
                "code" => $cod,
                "username" => $username,
                "cat" => $cat2,
                "type" => $Translate_type,
                );
            $translate_url = 'translate.php?' . http_build_query($params);
            //--------------------
            if ( $username != '' ) {
                // $tab = "<a href='translate.php?title=$title2&code=$cod&username=$username&cat=$cat2' class='w3-button w3-round-large w3-blue'>Translate</a>";
                $tab = "<a href='" . $translate_url . "' class='w3-button w3-round-large w3-blue'>Translate</a>";
            } else {
                $tab = "<a class='w3-button w3-round-large w3-red' href='login5.php?action=login'><span class='glyphicon glyphicon-log-in'></span> Login</a>";
            };
            //--------------------
            $list .= "
        <tr>
        <td class='num'>$cnt</td>
        <td class='link_container'><a target='_blank' href='$urle'>$title</a></td>
        <td class='num'>$asse</td>
        <td class='num'>$word</td>
        <td class='num'>$refs</td>
        <td class='num'>$tab</td>
        </tr>   
    " ;
            //--------------------
            $cnt++ ;
            //--------------------
        }
    }
    //--------------------
    $script = '' ;
    if ($script =='3') {  $script = ''; };
    //--------------------
    $last = "</tbody></table>
" ;
    return $frist . $list . $last . $script ;
    //--------------------
    }
//--------------------
$doit2 = false ;
//--------------------
if ( $code_lang_name != '' ) { $doit2 = true ; };
//--------------------
//==========================

function Get_it( $array, $key ) {
    $uu = $array[$key] != '' ? $array[$key] : $array->{$key};
    return $uu;
};
//==========================
function doApiQuery_localhost( $params ) {
    $endPoint = "https://"."mdwiki.org/w/api.php";
    test_print("<br>doApiQuery_localhost:<br>");
    $url = $endPoint . "?" . http_build_query( $params );

    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $output = curl_exec( $ch );
    curl_close( $ch );
    //------------------
    test_print("<br>output:<br>$output");
    //------------------
    $result = json_decode( $output, true );
    
    return $result;
};
//==========================
function get_categorymembers( $cat ) {
    //-------------------
    global $test;
    //-------------------
    $ch = null;
    //-------------------
    $params = array(
        "action" => "query",
        "list" => "categorymembers",
        "cmtitle" => "Category:$cat",
        "cmlimit" => "max",
        "cmtype" => "page|subcat",
        "format" => "json"
    );
    //-------------------
    // test_print("<br>params:" . htmlspecialchars( var_export( $params, 1 ) ) );
    //-------------------
    $endPoint = "https://mdwiki.org/w/api.php?";
    test_print("<br>params:<br>$endPoint" . http_build_query($params) ."<br>");
    //-------------------
    $resa = array();
    //-------------------
    // if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
        //--------------------
        // $RTTtext = file_get_contents("cash/RTT.json");
        // $RTT = json_decode ( $RTTtext ) ;
        //--------------------
        $resa = doApiQuery_localhost( $params );
        // return $RTT->list;
        //--------------------
    // } else {
    $resa = doApiQuery( $params , $ch );
    // };
    //-------------------
    $items = array();
    //-------------------
    $continue   = $resa->{"continue"};
    $cmcontinue = $continue->{"cmcontinue"};// "continue":{"cmcontinue":"page|434c4f42415a414d|60836",
    //-------------------
    $query = $resa->{"query"};
    $categorymembers = $query->{"categorymembers"};
    $categorymembers = $categorymembers != '' ? $categorymembers : array();
    //-------------------
    // print htmlspecialchars( var_export( $categorymembers, 1 ) );
    //-------------------
    //
    foreach( $categorymembers as $pages ){
        // echo( $pages->{"title"} . "\n" );
        if ($pages->{"ns"} == 0 or $pages->{"ns"} == 14) {
            $items[] = $pages->{"title"};
        };
    };
    //-------------------
    
    //-------------------
    // $tt = array();
    // $tt['items']    = $items;
    // $tt['continue'] = $cmcontinue;
    //-------------------
    test_print("<br>items size:" . sizeof($items) );
    //-------------------
    return $items;
    //-------------------
};
//======================
function get_cat_from_cach( $cat ) {
    $RTTtext = file_get_contents("cash/$cat.json");
    //--------------------
    $RTT = json_decode ( $RTTtext );
    //--------------------
    $liste = $RTT->list;
    //--------------------
	test_print("<br>get_cat_from_cach: liste size:" . sizeof($liste) );
    //--------------------
    return $liste;
    //--------------------
};
//======================
function get_cat_members_from_mdwiki( $cat ) {
    //--------------------
    if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
        //--------------------
        return get_cat_from_cach( $cat );
        //--------------------
    };
    //-------------------
    $all  = get_categorymembers( $cat );
    //--------------------
    if (sizeof($all) == 0) {
        return get_cat_from_cach( $cat );
    };
    //--------------------
    // $all      = Get_it($pae,'items');
    // $continue = Get_it($pae,'continue');
    //--------------------
    $titles = array();
    //--------------------
    $cats = array();
    //-------------------
    foreach( $all as $title ){
        // echo( $title . "\n" );
        $ss = strstartswithn($title , 'Category:');
        if ($ss) {
            $cats[] = $title;
        } else {
            $titles[] = $title;
        };
    };
    //-------------------
    test_print("<br>cats size:" . sizeof($cats) );
    //-------------------
    $cats2 = array();
    //-------------------
    foreach( $cats as $subcat ){
        $aa = get_categorymembers( $subcat );
        //-------------------
        foreach( $aa as $title ){
            // $titles[] = $title;
            $ss = strstartswithn($title , 'Category:');
            if ($ss) {
                $cats2[] = $title;
            } else {
                $titles[] = $title;
            };
        };
    };
    //-------------------
    $cats3 = array();
    //-------------------
    foreach( $cats2 as $subcat ){
        $aae = get_categorymembers( $subcat );
        //-------------------=
        foreach( $aae as $tit ){
            $ss = strstartswithn($tit , 'Category:');
            if ($ss) {
                $cats3[] = $tit;
            } else {
                $titles[] = $tit;
            };
        };
    };
    //-------------------
    test_print("<br>titles size:" . sizeof($titles) );
    test_print("<br>end of get_cat_members_from_mdwiki <br>===============================<br>");
    //-------------------
    return $titles;
    //-------------------
};
//======================
function get_cat_members_with_php( $cat , $depth , $code , $test ) {
    //--------------------
    global $medwiki_to_enwiki;
    //--------------------
    $members_to = get_cat_members_from_mdwiki( $cat );
    //-------------------
    test_print("<br>members_to size:" . sizeof($members_to) );
    //-------------------
    $members = array();
    //-------------------
    foreach( $members_to as $mr ) {
        //-----------------
        $mrno = $medwiki_to_enwiki->{$mr};
        //-----------------
        // $mrno = Get_it( $medwiki_to_enwiki, $mr );
        // if ($mrno != '') { test_print("<br>mrno: $mrno");};
        //-----------------
        $mrn = $mrno != '' ? $mrno : $mr;
        $members[] = $mrn;
    }; 
    //-------------------
    test_print("<br>members size:" . sizeof($members) );
    //-------------------
    $textfile_csv = "cash/$code" . "_exists.csv";
    //-------------------
    test_print("get:textfile_csv:$textfile_csv");
    //-------------------
    $text = file_get_contents($textfile_csv);
    //--------------------
    $exists = explode("\n",$text);
    //--------------------
    $missing = array_diff($members,$exists);
    //--------------------
    $exs_len = sizeof($members) - sizeof($missing);
    //--------------------
    $results = array(
        "diff"=> 0,
        "len_of_all"=> sizeof($members),
        "len_of_exists"=> $exs_len,
        "len_of_missing"=> sizeof($missing),
        "missing"=> $missing
    );
    //--------------------
    test_print("<br>end of get_cat_members_with_php <br>===============================<br>");
    //--------------------
    return $results;
    //--------------------
};
//==========================
//--------------------
if ( $doit and $doit2 ) {
    //--------------------
    if ($test) {
        print '$doit and $doit2:<br>';
    };
    //--------------------
    $items = array() ;
    //--------------------
    // if ($test == 'test') {
    //--------------------
    $items = get_cat_members_with_php( $cat , $depth , $code , $test ) ; # mdwiki pages in the cat
    // } else {
        // $items = get_cat_members_with_py( $cat , $depth , $code , $test ) ; # mdwiki pages in the cat
    // };
    //--------------------
    $len_of_all = $items->{'len_of_all'} ? $items->{'len_of_all'} : $items['len_of_all'];
    $len_of_all = $len_of_all != '' ? $len_of_all : 0 ;
    
    $len_of_exists_pages = $items->{'len_of_exists'} ? $items->{'len_of_exists'} : $items['len_of_exists'];
    $len_of_exists_pages = $len_of_exists_pages != '' ? $len_of_exists_pages : 0 ;
    
    $len_of_missing_pages = $items->{'len_of_missing'} ? $items->{'len_of_missing'} : $items['len_of_missing'];
    $len_of_missing_pages = $len_of_missing_pages != '' ? $len_of_missing_pages : 0 ;
    //--------------------
    print "<h4>Find $len_of_all pages in categories, $len_of_exists_pages exists, and $len_of_missing_pages missing in (<a href='https://$code.wikipedia.org'>https://$code.wikipedia.org</a>)." ;
    //--------------------
    $diff = $items->{'diff'} ? $items->{'diff'} : $items['diff'];
    if ($diff != '' ) {
        print(" diff:($diff)");
    };
    print "</h4>";
    //print var_dump($items) ;
    //--------------------
    $res_line = "Results " ;//. ($start+1) . "&ndash;" . ($start+$limit) ;
    //--------------------
    if ($test != '') { 
        $res_line .= 'test:';
    };
    //--------------------
    print "<h3>$res_line</h3>" ;
    //--------------------
    $items_missing = $items->{'missing'} ? $items->{'missing'} : $items['missing'];
    $table = make_table( $items_missing , $code , $cat ) ;
    //--------------------
    print $table ;
    //--------------------
    $misso  = $items->{'misso'} ? $items->{'misso'} : $items['misso'];
    if ($misso != '') {
        print "<h3>pages exists in mdwiki and missing in enwiki:</h3>" ;
        $table3 = make_table( $misso , $code , $cat ) ;
        print $table3;
    };
    //--------------------
    if ($test != '') {
        $enwiki_missing = $items->{'enwiki_missing'};
        $table2 = make_table( $enwiki_missing , $code , $cat ) ;
        print $table2 ;
    };
    //--------------------
} else {
    if (isset($doit) && isset($test) ) {
        //===============
        print "_REQUEST code:" . $_REQUEST['code'] . "<br>";
        print "code:$code<br>";
        print "code_lang_name:$code_lang_name<br>";
        //===============
    };
};
//--------------------
print "</main>
<!-- Footer -->";
//--------------------
if ( $doit ) {
    print "";
} else {
    print "
<footer class='app-footer'>
</footer>";
};
//--------------------
print "
</body>
</html>
</div>";
//--------------------
?>