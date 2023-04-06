<?PHP
//---
require('header.php');
require('langcode.php');
include_once('functions.php');
include_once('td_config.php');
$conf = get_configs('conf.json');
//---
$allow_whole_translate = isset($conf['allow_type_of_translate']) ? $conf['allow_type_of_translate'] : true;
//---
$code = isset($_REQUEST['code']) ? $_REQUEST['code'] : '';
//---
if ($code == 'undefined') $code = "";
//---
$code = isset($lang_to_code[$code]) ? $lang_to_code[$code] : $code;
$code_lang_name = isset($code_to_lang[$code]) ? $code_to_lang[$code] : ''; 
//---
$tra_type  = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
if ($allow_whole_translate == false) $tra_type = 'lead';
//---
$cat = isset($_REQUEST['cat']) ? $_REQUEST['cat'] : '';
//---
if ($cat == "undefined") $cat = "RTT";
//---
$cat_ch = htmlspecialchars($cat, ENT_QUOTES);
//---
$catinput_depth = array();
$catinput_list = array();
//---
$qq = execute_query('select category, display, depth from categories;');
//---
$numb = 0;
//---
foreach ( $qq AS $Key => $table ) {
    $numb += 1;
    $category = $table['category'];
    $display = $table['display'];
    $catinput_list[$display] = $category;
    //---
    $catinput_depth[$category] = $table['depth'];
    //---
};
//---
$depth  = isset($_REQUEST['depth']) ? $_REQUEST['depth'] : 1;
$depth  = $depth * 1 ;
//---
$depth  = isset($catinput_depth[$cat]) ? $catinput_depth[$cat] : 1;
//---
function print_form_start1() {
    //---
    global $allow_whole_translate ;
    global $lang_to_code, $catinput_list;
    global $cat_ch, $code_lang_name, $code, $username, $tra_type;
    //---
    $lead_checked = "checked";
    $all_checked = "";
    //---
    if ($tra_type == 'all') {
        $lead_checked = "";
        $all_checked = "checked";
    };
    //---
    $cate = $cat_ch != '' ? $cat_ch : 'RTT' ;
    //---
    $coco = $code_lang_name;
    if ( $coco == '') { $coco = $code ; };
    //---
    $lang_list = '';
    //---
    foreach ( $lang_to_code AS $langeee => $codr ) {
        $lang_list .= "
            <option data-tokens='$codr' value='$codr'>$langeee</option>";
    };
    //---
    $langse = "
      <input list='sLanguages' type='text' placeholder='two letter code' name='code' id='code' value='$coco' autocomplete='off' role='combobox' class='form-select' required>
          <datalist id='Languages' class='selectpickerr' role='listbox'>
              $lang_list
          </datalist>
      </input>
    ";
    //---
    $err = '';
    //---
    if ($code_lang_name == '' and $code != '') { 
        $err = "<span style='font-size:13pt;color:red'>code ($code) not valid wiki.</span>";
    } else {
        if ($code != '') { $_SESSION['code'] = $code; };
    };
    //---
    $uiu = '
    <a role="button" class="btn btn-primary" onclick="login()">
    <i class="fas fa-sign-in-alt fa-sm fa-fw mr-1"></i><span class="navtitles">Login</span>
    </a>';
    //---
    if ( $username != '' ) $uiu = '<input type="submit" name="doit" class="btn btn-primary" value="Do it"/>';
    //---
    $catinput = make_drop($catinput_list, $cate);
    //---
    $catinput = "
    <select dir='ltr' id='cat' name='cat' class='form-select'>
        $catinput
    </select>";
    //---
    $ttype = "
    <div class='form-check form-check-inline'>
      <input type='radio' class='form-check-input' id='customRadio' name='type' value='lead' $lead_checked>
      <label class='form-check-label' for='customRadio'>The lead only</label>
    </div>
    <div class='form-check form-check-inline'>
      <input type='radio' class='form-check-input' id='customRadio2' name='type' value='all' $all_checked>
      <label class='form-check-label' for='customRadio2'>The whole article</label>
    </div>";
    //---
	$col12 		= 'col-lg-10 col-md-10';
	$gridclass 	= 'input-group col-7 mb-3';
    //---
	$d2 = "
        <div class='$col12'>
            <div class='form-group'>
                <div class='$gridclass'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>%s</span>
                    </div>
					      %s
                </div>
            </div>
        </div>";
    //---
	$in_cat = sprintf($d2, 'Translation campaign', $catinput);
	//---
	$in_lng  = sprintf($d2, 'Target language', "<div>$langse $err</div>");
	//---
	$in_typ = '';
    if ($allow_whole_translate == true) { 
        $in_typ = sprintf($d2, 'Type', "<div class='form-control'>$ttype</div>");
    } else {
        $in_typ = "<input name='type' value='lead' hidden/>";
    };
    //---
    $d = "
    <div class='row'>
        $in_cat
        $in_lng
        $in_typ
        <div class='$col12'>
            <h4 class='aligncenter'>
            $uiu
            </h4>
        </div>
    </div>
    ";
    //---
    return $d;
    //---
};
//---
$img_src = '//upload.wikimedia.org/wikipedia/commons/thumb/5/58/Wiki_Project_Med_Foundation_logo.svg/400px-Wiki_Project_Med_Foundation_logo.svg.png';
//---
$form_start1  = print_form_start1();
//---
$intro = "This tool looks for Wikidata items that have a page on mdwiki.org but not in another wikipedia language <a href='?cat=RTT&depth=1&code=ceb&doit=Do+it'><b>(Example)</b></a>.";
//---
$nan = "
<div class='container'>
  <div class='card'>
    <div class='card-header aligncenter' style='font-weight:bold;'></div>
    <div class='card-body'>
      <div class='mainindex'>
		<div style='float:right'>
          <img class='medlogo' src='$img_src' decoding='async' alt='Wiki Project Med Foundation logo'>
        </div>
        <h6>
          $intro
        </h6>
        <p><a href='//mdwiki.org/wiki/WikiProjectMed:Translation_task_force'><b>How to use.</b></a></p>

        <form method='GET' action='index.php' class='form-inline'>
          %s
        </form>

      </div>
    </div>
  </div>
</div>
";
//---
echo sprintf($nan, $form_start1);
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
require('foter.php');
//---
?>
