<?PHP
//---
require('header.php');
echo '<script src="sorttable.js"></script>';
// require('tables.php'); 
require('langcode.php');
include_once('functions.php');
//---
$doit = isset($_REQUEST['doit']);
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
        <input size=15 list='sLanguages' type='text' placeholder='two letter code' name='code' id='code' value='$coco' autocomplete='off' role='combobox' class='code_11'>
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
    //---<a class="btn btn-danger" href="login5.php?action=login"> Login</a>
    $uiu = '
	<a role="button" class="btn btn-primary" onclick="login()">
		<i class="fas fa-sign-in-alt fa-sm fa-fw mr-1"></i><span class="navtitles">Login</span>
	</a>';
    //---
    // $uiu = '<button class="btn btn-primary" onclick="login()"><i class="fas fa-sign-in-alt fa-sm fa-fw mr-1"></i><span class="navtitles">Login</span> </button>';
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
    $d = "
	<form method='GET' action='index.php' class='form-inline'>
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
require('results.php');
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
    } else {
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
