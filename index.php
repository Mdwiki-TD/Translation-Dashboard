<?PHP

//--------------------
require('header1.php');
require('tables.php');
require('langcode.php');
require('functions.php');
//--------------------
$doit = isset($_REQUEST['doit']);
$test = $_REQUEST['test'];
//--------------------
$prefilled_requests = [] ;
//--------------------
$code = strtolower($_REQUEST['code']);
$code = isset($lang_to_code[$code]) ? $lang_to_code[$code] : $code;
$code_lang_name = $code_to_lang[$code]; 
//--------------------
$depth  = $_REQUEST['depth'] * 1 ;
$cat    = $_REQUEST['cat'];
$cat_ch = htmlspecialchars($cat,ENT_QUOTES);
//--------------------
function print_index_start() {
	return '
<div class="col-md-10 col-md-offset-1" align=left >
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
	$coco = assert($code_lang_name) ? $code_lang_name : $code;
    //--------------------
	$str = '';
    //--------------------
    $str .= "<input list='Languages' class='span2' type='text' placeholder='two letter code' name='code' id='code' value='$coco'>";
    //--------------------
    $str .= '<datalist id="Languages">';
    //--------------------
    foreach ( $lang_to_code AS $lange => $cod ) {
        $str .= "<option value='$cod'>$lange</option>";
    };
    //--------------------
    $str .= '</datalist></input>
    ' ;
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
	global $cat_ch,$depth,$code_lang_name,$code;
	//--------------------
	$d = '';
	//--------------------
	$d .= "
<form method='get' class='form-inline'>
<hr/>
<table class='table-condensed' border=0><tbody>
<tr>
<td><b>Mdwiki category</b></td>
<td>
<input class='span4' type='text' name='cat' id='cat' value='$cat_ch' placeholder='Root category' />, depth <input class='span1' name='depth' type='text' value='$depth' /> (optional)
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
	$d .= '</td></tr>
<tr><td colspan="3" class="aligncenter">
';
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
print print_index_start();
//--------------------
print print_form_start();
//--------------------
if ( $username != '' ) {
        print '<input type="submit" name="doit" class="btn w3-button w3-round-large w3-blue" value="Do it"/>';
    } else {
        print "
<a class='btn w3-button w3-round-large w3-red inputsubmit' href='login2.php?action=login'>Login</a>";

};
print print_form_last();
//==========================
function get_cat_members_with_py( $cat , $depth ) {
    global $test,$code;
    //--------------------
    $cat3 = ucfirst(trim($cat));
    $cat3 = str_replace ( ' ' , '_' , $cat3 );
    $dd = "python catdepth2.py -cat:$cat3 -depth:$depth -code:$code" ;
    //$dd = "python catdepth.py -cat:$cat3 -depth:$depth -code:$code" ;
    //--------------------
    if ( $test != '') { 
        $dd = $dd . ' test';
        };
    //--------------------
    if ($_SERVER['SERVER_NAME'] != 'mdwiki.toolforge.org') { 
        $dd = $dd . ' local';
        echo $dd;
        echo '<br>';
        };
    //--------------------
    $command = escapeshellcmd( $dd );
    $output = shell_exec($command);
    //--------------------
    $items = json_decode ( $output ) ;
    //--------------------
    // if ($_SERVER['SERVER_NAME'] != 'mdwiki.toolforge.org') { 
        // echo var_dump($output);
        // echo '<br>' ;
        // };
    //--------------------
    return $items;
    //--------------------
}
//==========================
function make_table( $items , $cod , $cat ) {
    global $username,$Words_table,$Assessments_table,$Assessments_fff ;
    $frist = '<table class="sortable table table-striped" id="main_table">';
    
    //$frist = '<table class="table table-sm table-striped" id="main_table">';
    //$frist .= "<caption>$res_line</caption>";
    $frist .= '
<thead><tr>
<th onclick="sortTable(0)" class="num">#</th>
<th onclick="sortTable(1)" class="text-nowrap" tt="h_title">Title</th>
<th onclick="sortTable(2)" class="text-nowrap" tt="h_len">Importance</th>
<th onclick="sortTable(3)" class="text-nowrap" tt="h_len">Words</th>
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
        $kry = assert($Assessments_fff[$aa]) ? $Assessments_fff[$aa] : $Assessments_fff['Unknown'];
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
            $asse = $Assessments_table->{$title}; 
            if ( $asse == '' ) { $asse = 'Unknown'; }; 
            //--------------------
            if ( $username != '' ) {
                $tab = "<a href='translate.php?title=$title2&code=$cod&username=$username&cat=$cat2' class='w3-button w3-round-large w3-blue'>Translate</a>";
            } else {
                $tab = "<a class='w3-button w3-round-large w3-red' href='login2.php?action=login'>Login</a>";
            };
            
            $list .= "
        <tr>
        <td class='num'>$cnt</td>
        <td class='link_container'><a target='_blank' href='$urle'>$title</a></td>
        <td class='num'>$asse</td>
        <td class='num'>$word</td>
        <td class='num'>$tab</td>
        </tr>   
    " ;
            $cnt++ ;
        }
    }
    //--------------------
    $script = '' ;
    if ($script =='3') { 
        $script = '';
    }
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
if ( $doit  and $doit2 ) {
    //--------------------
    print "" ;
    //--------------------
    //$items = array() ;
    //--------------------
    $items = get_cat_members_with_py( $cat , $depth ) ; # mdwiki pages in the cat
    //--------------------
    $len_of_all = $items->{'len_of_all'} ? $items->{'len_of_all'} : $items['len_of_all'];
    $len_of_all = $len_of_all != '' ? $len_of_all : 0 ;
    
    $len_of_exists_pages = $items->{'len_of_exists'};
    $len_of_exists_pages = $len_of_exists_pages != '' ? $len_of_exists_pages : 0 ;
    
    $len_of_missing_pages = $items->{'len_of_missing'};
    $len_of_missing_pages = $len_of_missing_pages != '' ? $len_of_missing_pages : 0 ;
    //--------------------
    print "<h4>Find $len_of_all pages in categories, $len_of_exists_pages exists, and $len_of_missing_pages missing in (<a href='https://$code.wikipedia.org'>https://$code.wikipedia.org</a>)." ;
    //--------------------
    $diff = $items->{'diff'};
    if ($diff != '' ) {
        print(" diff:($diff)");
    };
    print "</h4>";
    //print var_dump($items) ;
    //--------------------
    $res_line = "Results " ;//. ($start+1) . "&ndash;" . ($start+$limit) ;
    print "<h3>$res_line</h3>" ;
    //--------------------
    $items_missing = $items->{'missing'};
    $table = make_table( $items_missing , $code , $cat ) ;
    //--------------------
    print $table ;
    //--------------------
    $misso  = $items->{'misso'};
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