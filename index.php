<?PHP
//---
require('header.php');
echo '<script src="sorttable.js"></script>';
require('tables.php'); 
require('langcode.php');
// include_once('functions.php');
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
function print_form_start() {
    //---
    global $lang_to_code, $doit;
    global $cat_ch, $depth, $code_lang_name, $code, $username, $Translate_type;
    //---
    $lead_checked = "checked";
    $all_checked = "";
    // -------------
    if ($Translate_type == 'all') {
        $lead_checked = "";
        $all_checked = "checked";
    };
    //---
    $cate = $cat_ch != '' ? $cat_ch : 'RTT' ;
    //---
    $do = '';
    //---
    if ($username != '' ) $do = "<form method='GET' action='index.php' class='form-inline'>";
    //---
    $coco = $code_lang_name;
    if ( $coco == '') { $coco = $code ; };
    //---
    //======
    $lang_list = '';
    //---
    foreach ( $lang_to_code AS $langeee => $codr ) {
        $lang_list .= "
            <option value='$codr'>$langeee</option>";
    };
    //---
    // $lang_list = make_drop($lang_to_code,$code, 'code');
    //---
    $langse = "
        <input size=25 list='sLanguages' type='text' placeholder='two letter code' name='code' id='code' value='$coco' autocomplete='off' role='combobox' class='code_11'>
            <datalist id='Languages' class='selectpickerr' role='listbox'>
                $lang_list
            </datalist>
        </input>
    ";
    //======
    $err = '';
    //---
    if ($code_lang_name == '' and $code != '') { 
        $err = "<span style='font-size:13pt;color:red'>code ($code) not valid wiki.</span>";
    } elseif ( $code == '' and $doit != '' ) { 
        $err = "<span style='font-size:13pt;color:red'>enter wiki code.</span>";
    } else {
        if ($code != '') { $_SESSION['code'] = $code; };
    };
    //---
    $uiu = '<button type="submit" class="btn btn-default" name="action" value="login"><span class="glyphicon glyphicon-log-in"></span> Login </button>';
    // -------------
    if ( $username != '' ) $uiu = '<input type="submit" name="doit" class="btn btn-primary" value="Do it"/>';
    //---
    $catinput_list = array(
        // "ready to translate" => 'RTT',
        // "Covid team" => 'RTTCovid',
        );
	//---
	$qq = quary2('select category, display from categories;');
	//---
    $numb = 0;
	//---
	foreach ( $qq AS $Key => $table ) {
		$numb += 1;
		$category = $table['category'];
		$display = $table['display'];
		$catinput_list[$display] = $category;
	};
    #---
    $catinput = make_drop($catinput_list, $cate, 'cat');
    //---
    // $catinput = "<input class='span4' type='text' size=25 name='cat' id='cat' value='$cate' placeholder='Root category' /> <!-- Depth <input name='depth' type='number' size='1' min=0 max=10 value='$depth' /> (optional) -->";
    //---
    $d = "$do
    
    <table class='table-sm' border='0'>
	  <tbody>
		<tr>
		  <td><b>Translation campaign</b></td>
		  <td>$catinput</td>
		</tr>
		<tr>
		  <td class='spannowrap'><b>Target language</b></td>
		  <td style='width:100%'>
			$langse
			$err
		  </td>
		</tr>
		<tr>
		  <td>
            <b>Type</b>
		  </td>
		  <td colspan='2'>
			<input type='radio' name='type' value='lead' $lead_checked>The lead only
			<br>
			<input type='radio' name='type' value='all' $all_checked>The whole article
		  </td>
		</tr>
		<tr>
		  <td colspan='3' class='aligncenter'>
			$uiu
		  </td>
		</tr>
	  </tbody>
	</table>
    
    </form>";
    //---
    return $d;
    // -------------
};
//---
$img_src = '//upload.wikimedia.org/wikipedia/commons/thumb/5/58/Wiki_Project_Med_Foundation_logo.svg/400px-Wiki_Project_Med_Foundation_logo.svg.png';
//---
$form_start = print_form_start();
//---
echo "
<div class='card'>
	<div class='card-header aligncenter' style='font-weight:bold;'></div>
	<div class='card-body'>
		<div class='mainindex' align='left'>
			<div style='float:right'>
				<img class='medlogo' src='$img_src' decoding='async' alt='Wiki Project Med Foundation logo'>
			</div>
			<p>This tool looks for Wikidata items that have a page on mdwiki.org but not in another wikipedia language <a href='?cat=RTT&depth=1&code=ceb&doit=Do+it'><b>(Example)</b></a>.</p>
			<p><a href='//mdwiki.org/wiki/WikiProjectMed:Translation_task_force'><b>How to use.</b></a></p>
			$form_start
		</div>
	</div>
</div>
";
//---
//===
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
//===
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
        print "_REQUEST code:" . $_REQUEST['code'] . "<br>";
        print "code:$code<br>";
        print "code_lang_name:$code_lang_name<br>";
        //===
    };
    echo '</div>';
};
//---
?>
<script>
code.onfocus = function () {
  Languages.style.display = 'block';
  code.style.borderRadius = '5px 5px 0 0';  
};
for (let option of Languages.options) {
  option.onclick = function () {
    code.value = option.value;
    Languages.style.display = 'none';
    code.style.borderRadius = '5px';
  }
};

code.oninput = function() {
  currentFocus = -1;
  var text = code.value.toUpperCase();
  for (let option of Languages.options) {
    if(option.value.toUpperCase().indexOf(text) > -1){
      option.style.display = 'block';
  }else{
    option.style.display = 'none';
    }
  };
}
var currentFocus = -1;
code.onkeydown = function(e) {
  if(e.keyCode == 40){
    currentFocus++
   addActive(Languages.options);
  }
  else if(e.keyCode == 38){
    currentFocus--
   addActive(Languages.options);
  }
  else if(e.keyCode == 13){
    e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the 'active' item:*/
          if (Languages.options) Languages.options[currentFocus].click();
        }
  }
}

function addActive(x) {
    if (!x) return false;
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    x[currentFocus].classList.add('active');
  }
  function removeActive(x) {
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove('active');
    }
  }

</script>
<?php
//---
if ($_REQUEST['test'] != '' ) echo "load " . __file__ . " true.";
//---
require('foter.php');
//---
?>
