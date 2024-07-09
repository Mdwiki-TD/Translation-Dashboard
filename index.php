<?PHP
//---
namespace TD;

if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
include_once __DIR__ . '/header.php';
include_once __DIR__ . '/Tables/langcode.php';
include_once __DIR__ . '/actions/functions.php';
include_once __DIR__ . '/Tables/sql_tables.php';
//---
// $conf = get_configs('conf.json');
//---
$allow_whole_translate = $settings['allow_type_of_translate']['value'] ?? '1';
//---
$req  = load_request();
$code = $req['code'];
//---
$cat  = ($req['cat'] != '') ? $req['cat'] : $main_cat;
$camp  = ($req['camp'] != '') ? $req['camp'] : $main_camp;
//---
$code_lang_name = $req['code_lang_name'];
//---
$tra_type  = $_REQUEST['type'] ?? '';
if ($allow_whole_translate == '0') $tra_type = 'lead';
//---
$cat_ch = htmlspecialchars($cat, ENT_QUOTES);
$camp_ch = htmlspecialchars($camp, ENT_QUOTES);
//---
function print_form_start1($allow_whole_translate, $lang_to_code, $catinput_list, $campaign_input_list, $cat_ch, $camp_ch, $code_lang_name, $code, $tra_type)
{
    //---
    $lead_checked = "checked";
    $all_checked = "";
    //---
    if ($tra_type == 'all') {
        $lead_checked = "";
        $all_checked = "checked";
    };
    //---
    $coco = $code_lang_name;
    if ($coco == '') {
        $coco = $code;
    };
    //---
    $lang_list = '';
    //---
    foreach ($lang_to_code as $langeee => $codr) {
        $selected = ($codr == $code) ? 'selected' : '';
        $lang_list .= <<<HTML
            <option data-tokens='$codr' value='$codr' $selected>$langeee</option>
            HTML;
    };
    //---
    $langse = <<<HTML
        <select aria-label="Language code"
            class="selectpicker"
            id='code'
            name='code'
            placeholder='two letter code'
            data-live-search="true"
            data-container="body"
            data-live-search-style="begins"
            data-bs-theme="auto"
            data-style='btn active'
            data-width="100%"
            required>
            $lang_list
        </select>
    HTML;
    //---
    $err = '';
    //---
    if ($code_lang_name == '' and $code != '') {
        $err = "<span style='font-size:13pt;color:red'>code ($code) not valid wiki.</span>";
    } else {
        if ($code != '') {
            $_SESSION['code'] = $code;
        };
    };
    //---
    $uiu = <<<HTML
            <a role="button" class="btn btn-outline-primary" onclick="login()">
            <i class="fas fa-sign-in-alt fa-sm fa-fw mr-1"></i><span class="navtitles">Login</span>
            </a>
    HTML;
    //---
    if ( global_username != '' ) {
        $uiu = '<input type="submit" name="doit" class="btn btn-outline-primary" value="Do it"/>';
    }
    //---
    $cat_input = make_drop($catinput_list, $cat_ch);
    $camp_input = make_drop($campaign_input_list, $camp_ch);
    //---
    $cat_input = <<<HTML
        <select dir='ltr' name='cat' id='cat' class='form-select' data-bs-theme="auto">
            $cat_input
        </select>
    HTML;
    //---
    $camp_input = <<<HTML
        <select dir='ltr' name='camp' id='camp' class='form-select' data-bs-theme="auto">
            $camp_input
        </select>
    HTML;
    //---
    $ttype = <<<HTML
        <div class='form-check form-check-inline'>
            <input type='radio' class='form-check-input' id='customRadio' name='type' value='lead' $lead_checked>
            <label class='form-check-label' for='customRadio'>The lead only</label>
        </div>
        <div class='form-check form-check-inline'>
            <input type='radio' class='form-check-input' id='customRadio2' name='type' value='all' $all_checked>
            <label class='form-check-label' for='customRadio2'>The whole article</label>
        </div>
    HTML;
    //---
    $col12         = 'col-10';
    $gridclass     = 'input-group col-7 mb-3';
    //---
    $d2 = <<<HTML
        <div class='$col12'>
            <div class='$gridclass'>
                <span class='input-group-text' for="%s">%s</span>
                %s
            </div>
        </div>
    HTML;
    //---
    $d22 = <<<HTML
        <div class='$col12'>
            <div class="mb-3">
                <label for="%s" class="form-label"><b>%s</b></label>
                %s
            </div>
        </div>
    HTML;
    //---
    $in_cat = sprintf($d22, 'cat', 'Category', $cat_input);
    $in_camp = sprintf($d22, 'camp', 'Campaign', $camp_input);
    //---
    $in_lng = sprintf($d22, 'code', 'Language', "<div>$langse $err</div>");
    //---
    $in_typ = '';
    if ($allow_whole_translate == '1') {
        $in_typ = sprintf($d22, 'type', 'Type', "<div class='form-control'>$ttype</div>");
    } else {
        $in_typ = "<input name='type' value='lead' hidden/>";
    };
    //---
    $d = <<<HTML
    <div class='row'>
        <!-- $in_cat -->
        $in_camp
        $in_lng
        $in_typ
        <div class='$col12'>
            <h4 class='aligncenter mb-0'>
                $uiu
            </h4>
        </div>
    </div>

    HTML;
    //---
    return $d;
    //---
};
//---
$img_src = '//upload.wikimedia.org/wikipedia/commons/thumb/5/58/Wiki_Project_Med_Foundation_logo.svg/400px-Wiki_Project_Med_Foundation_logo.svg.png';
//---
$form_start1  = print_form_start1($allow_whole_translate, $lang_to_code, $catinput_list, $campaign_input_list, $cat_ch, $camp_ch, $code_lang_name, $code, $tra_type);
//---
$intro = <<<HTML
    This tool looks for Wikidata items that have a page on mdwiki.org but not in another wikipedia language <a href='?cat=RTT&depth=1&code=ceb&doit=Do+it'>(Example)</a>. <a href='//mdwiki.org/wiki/WikiProjectMed:Translation_task_force'><b>How to use.</b></a>
HTML;
//---
echo <<<HTML
<div class='container'>
  <div class='card'>
    <div class='card-header'>
        $intro
    </div>
    <div class='card-body mb-0'>
      <div class='mainindex'>
		<div style='float:right'>
            <img class='medlogo' src='$img_src' decoding='async' alt='Wiki Project Med Foundation logo'>
        </div>
        <form method='GET' action='index.php' class='form-inline'>
            $form_start1
        </form>
      </div>
    </div>
  </div>
</div>
<!-- <script src='/Translation_Dashboard/js/codes.js'></script> -->
HTML;
//---
include_once __DIR__ . '/results/results.php';
//---
include_once __DIR__ . '/foter.php';
//---
