<?PHP
//---
namespace TD\Render\Forms;

/*
Usage:

use function TD\Render\Forms\print_form_start1;

*/

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function make_drop($uxutable, $code)
{
    $options  =  "";
    //---
    foreach ($uxutable as $name => $cod) {
        $cdcdc = $code == $cod ? "selected" : "";
        $options .= <<<HTML
		<option value='$cod' $cdcdc>$name</option>

		HTML;
    };
    //---
    return $options;
}
function print_form_start1($allow_whole_translate, $Lang_tables, $cat_input_list, $campaigninput_list, $cat_ch, $camp_ch, $code_lang_name, $code, $tra_type)
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
    if (empty($coco)) {
        $coco = $code;
    };
    //---
    $lang_list = '';
    //---
    foreach ($Lang_tables as $_ => $lang_tab) {
        $lang_code = $lang_tab['code'] ?? "";
        $lang_name = $lang_tab['autonym'] ?? "";
        $lang_title = "($lang_code) $lang_name";
        $selected = ($lang_code == $code) ? 'selected' : '';
        $lang_list .= <<<HTML
            <option data-tokens='$lang_code' value='$lang_code' $selected>$lang_title</option>
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
    if (empty($code_lang_name) and !empty($code)) {
        $err = "<span style='font-size:13pt;color:red'><span tt='code_not_valid_wiki' tt1='$code'></span></span>";
    } else {
        if (!empty($code)) {
            $_SESSION['code'] = $code;
        };
    };
    //---
    $uiu = <<<HTML
            <a role="button" class="btn btn-outline-primary" onclick="login()">
            <i class="fas fa-sign-in-alt fa-sm fa-fw mr-1"></i><span class="navtitles"><span tt="login"></span></span>
            </a>
    HTML;
    //---
    if ($GLOBALS['global_username'] != '') {
        $uiu = '<input type="submit" name="doit" class="btn btn-outline-primary" tt_value="do_it"/>';
    }
    //---
    $cat_input = make_drop($cat_input_list, $cat_ch);
    $camp_input = make_drop($campaigninput_list, $camp_ch);
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
            <label class='form-check-label' for='customRadio'><span tt="the_lead_only"></span></label>
        </div>
        <div class='form-check form-check-inline'>
            <input type='radio' class='form-check-input' id='customRadio2' name='type' value='all' $all_checked>
            <label class='form-check-label' for='customRadio2'><span tt="the_whole_article"></span></label>
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
    $in_cat = sprintf($d22, 'cat', '<span tt="category"></span>', $cat_input);
    $in_camp = sprintf($d22, 'camp', '<span tt="campaign"></span>', $camp_input);
    //---
    $in_lng = sprintf($d22, 'code', '<span tt="language"></span>', "<div>$langse $err</div>");
    //---
    $in_typ = '';
    if ($allow_whole_translate == '1') {
        $in_typ = sprintf($d22, 'type', '<span tt="translation_type"></span>', "<div class='form-control'>$ttype</div>");
    } else {
        $in_typ = '<input type="hidden" name="type" value="lead" />';
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
