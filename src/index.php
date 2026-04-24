<?php

namespace TD;

// =======================
// Debug Mode
// =======================
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// =======================
// Includes
// =======================
include_once __DIR__ . '/include_all.php';
include_once __DIR__ . '/header.php';
include_once __DIR__ . '/backend/loaders/load_request.php';

use Tables\Main\MainTables;
use function Loaders\LoadRequest\load_request;
use function Results\ResultsIndex\results_loader;
use function SQLorAPI\GetDataTab\get_td_or_sql_categories;
use function SQLorAPI\GetDataTab\get_td_or_sql_settings;

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

function print_form_start1($Lang_tables, $code)
{
    //---
    $lang_list = '';
    //---
    foreach ($Lang_tables as $_ => $lang_tab) {
        $lang_code = $lang_tab['code'] ?? "";
        $lang_name = $lang_tab['autonym'] ?? "";

        if (empty($lang_code)) continue;

        $lang_title = "($lang_code) $lang_name";
        $selected = ($lang_code == $code) ? 'selected' : '';
        $lang_list .= <<<HTML
            <option data-tokens='$lang_code' value='$lang_code' $selected>$lang_title</option>
        HTML;
    };
    return $lang_list;
};

// =======================
// Load Config
// =======================

$settings = get_td_or_sql_settings();
$categories_tab = get_td_or_sql_categories();

$settings = array_column($settings, 'value', 'title');

$allow_whole_translate = $settings['allow_type_of_translate'] ?? '1';
$load_new_result       = $settings['load_new_result'] ?? '';

$s_campaign_input_list = [];

$s_main_cat = "";
$s_main_camp = "";

foreach ($categories_tab as $k => $tab) {
    if (!empty($tab['category']) && !empty($tab['campaign'])) {
        $s_campaign_input_list[$tab['campaign']] = $tab['campaign'];
        $is_default  = $tab['is_default'];
        if ($is_default == 1 || $is_default == '1') $s_main_cat = $tab['category'];
        if ($is_default == 1 || $is_default == '1') $s_main_camp = $tab['campaign'];
    };
};

// =======================
// Load Request
// =======================
$req = load_request($s_campaign_input_list, $allow_whole_translate);

$test              = $req['test'] ?? '';
$code              = $req['code'] ?? '';
$tra_type          = $req['tra_type'] ?? '';
$filter_sparql     = $req['filter_sparql_x'] ?? true;
$code_lang_name    = $req['code_lang_name'] ?? '';
$errors            = $req['errors'];

$cat  = $req['cat']  ?: $s_main_cat;
$camp = $req['camp'] ?: $s_main_camp;

// =======================
// Normalize Data
// =======================


$global_username = $GLOBALS['global_username'] ?? '';
$user_coord      = $GLOBALS['user_is_coordinator'] ?? false;

// =======================
// UI
// =======================

// Form
$in_typ = '<input type="hidden" name="type" value="lead" />';

if ($allow_whole_translate == '1') {

    $lead_checked = "checked";
    $all_checked = "";

    if ($tra_type == 'all') {
        $lead_checked = "";
        $all_checked = "checked";
    };

    $in_typ = <<<HTML
        <div class='col-10'>
            <div class="mb-3">
                <label for="type" class="form-label"><b>Type</b></label>
                <div class='form-control'>
                    <div class='form-check form-check-inline'>
                        <input type='radio' class='form-check-input' id='customRadio' name='type' value='lead' $lead_checked>
                        <label class='form-check-label' for='customRadio'>The lead only</label>
                    </div>
                    <div class='form-check form-check-inline'>
                        <input type='radio' class='form-check-input' id='customRadio2' name='type' value='all' $all_checked>
                        <label class='form-check-label' for='customRadio2'>The whole article</label>
                    </div>
                </div>
            </div>
        </div>
    HTML;
};

$camp_ch = htmlspecialchars($camp, ENT_QUOTES);
$camp_input = make_drop($s_campaign_input_list, $camp_ch);

if ($camp === "test") {
    $camp_input .= "<option value='test' selected>test</option>";
};


$lang_list = print_form_start1(MainTables::$x_Langs_table, $code);

// Login Button
$login_btn = (!empty($global_username))
    ? '<input type="submit" name="doit" class="btn btn-outline-primary" value="Do it"/>'
    : <<<HTML
    <input type="hidden" name="doit" value="Do it"/>
    <button type="submit"
            formaction="/auth/login.php"
            formmethod="get"
            formnovalidate
            class="btn btn-outline-primary">
        <i class="fas fa-sign-in-alt"></i> Login
    </button>
HTML;

// Errors HTML
$error_html = '';
foreach ($errors as $err) {
    $error_html .= "<div class='text-danger' style='font-size:13pt;'>$err</div>";
}

// =======================
// Render Header Block
// =======================
echo <<<HTML
    <div class='container'>
        <div class='card'>
            <div class='card-header'>
                This tool looks for Wikidata items that have a page on mdwiki.org but not in another wikipedia language
                <a href='?cat=RTT&depth=1&code=ceb&doit=Do+it'>(Example)</a>.
                <a href='//mdwiki.org/wiki/WikiProjectMed:Translation_task_force'><b>How to use.</b></a>
            </div>

            <div class='card-body mb-0'>
            <div class='mainindex'>
                <form method='GET' action='index.php' class='form-inline' id="mainForm">
                    <div class='row'>
                        <div class='col-10'>
                            <div class="mb-3">
                                <label for="camp" class="form-label"><b>Campaign</b></label>
                                <select dir='ltr' name='camp' id='camp' class='form-select' data-bs-theme="auto">
                                    $camp_input
                                </select>
                            </div>
                        </div>
                        <div class='col-10'>
                            <div class="mb-3">
                                <label for="code" class="form-label"><b>Language</b></label>
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
                            </div>
                        </div>
                        $in_typ
                        $error_html
                        <div class='col-10'>
                            <h4 class='aligncenter mb-0'>
                                $login_btn
                            </h4>
                        </div>
                    </div>
                </form>
                <div class="d-flex justify-content-end">
                    <img class='med-logo-big' src='/favicon.svg' alt='Wiki Project Med Foundation logo'>
                </div>
            </div>
            </div>
        </div>
    </div>
HTML;

// =======================
// Results
// =======================
echo "<div class='container-fluid'>";

// $doit     = $req['doit'] ?? false;
// if ($doit) {
if ($camp && $code) {
    $data = [
        "camp" => $camp,
        "code" => $code,
        "code_lang_name" => $code_lang_name,
        "cat" => $cat,
        "tra_type" => $tra_type,
        "global_username" => $global_username,
        "filter_sparql" => $filter_sparql,
        "new_result" => $load_new_result,
        "user_coord" => $GLOBALS['user_is_coordinator'] ?? false,
        "test" => $test
    ];

    echo results_loader($data);
}

echo "</div><br>";

// =======================
// Footer
// =======================
require_once __DIR__ . '/footer.php';
