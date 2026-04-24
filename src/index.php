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
use Tables\SqlTables\TablesSql;
use function Loaders\LoadRequest\load_request;
use function TD\Render\Forms\print_form_start1;
use function Results\ResultsIndex\results_loader;

// =======================
// Load Config
// =======================
$settings = TablesSql::$s_settings;

$allow_whole_translate = $settings['allow_type_of_translate']['value'] ?? '1';
$load_new_result       = $settings['load_new_result']['value'] ?? '';

// =======================
// Load Request
// =======================
$req = load_request();

$test              = $req['test'] ?? '';
$code              = $req['code'] ?? '';
$tra_type          = $req['tra_type'] ?? '';
$filter_sparql     = $req['filter_sparql_x'] ?? true;
$code_lang_name    = $req['code_lang_name'] ?? '';

$cat  = $req['cat']  ?: TablesSql::$s_main_cat;
$camp = $req['camp'] ?: TablesSql::$s_main_camp;

// =======================
// Normalize Data
// =======================
if ($allow_whole_translate == '0') {
    $tra_type = 'lead';
}

$global_username = $GLOBALS['global_username'] ?? '';
$user_coord      = $GLOBALS['user_is_coordinator'] ?? false;

// =======================
// Validation
// =======================
$errors = [];

if (empty($code_lang_name) && !empty($code)) {
    $errors[] = "code ($code) not valid wiki.";
    $code = "";
} elseif (!empty($code)) {
    $_SESSION['code'] = $code;
}

if (!in_array($camp, TablesSql::$s_campaign_input_list)) {
    $errors[] = "camp ($camp) not valid.";
    $camp = "";
}

// =======================
// UI
// =======================

// Form
$form_start = print_form_start1(
    $allow_whole_translate,
    MainTables::$x_Langs_table,
    TablesSql::$s_campaign_input_list,
    $cat,
    $camp,
    $code,
    $tra_type
);

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
                        $form_start
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
        "mobile_td" => $_GET["mobile_td"] ?? "1",
        "test" => $test
    ];

    echo results_loader($data);
}

echo "</div><br>";

// =======================
// Footer
// =======================
require_once __DIR__ . '/footer.php';
