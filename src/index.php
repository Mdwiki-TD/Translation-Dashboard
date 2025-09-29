<?PHP
//---
namespace TD;
//---
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
include_once __DIR__ . '/include_all.php';
include_once __DIR__ . '/header.php';
//---
include_once __DIR__ . '/backend/loaders/load_request.php';
//---
use Tables\Main\MainTables;
use Tables\SqlTables\TablesSql;
use function Loaders\LoadRequest\load_request;
use function TD\Render\Forms\print_form_start1;
use function Results\ResultsIndex\results_loader;
//---
$allow_whole_translate = TablesSql::$s_settings['allow_type_of_translate']['value'] ?? '1';
//---
$req  = load_request();
// ---
$doit     = $req['doit'] ?? false;
$test     = $req['test'] ?? "";
$code     = $req['code'] ?? "";
$tra_type = $req['tra_type'] ?? "";
$filter_sparql = $req['filter_sparql_x'] ?? true;
//---
$cat  = (!empty($req['cat'])) ? $req['cat'] : TablesSql::$s_main_cat;
$camp  = (!empty($req['camp'])) ? $req['camp'] : TablesSql::$s_main_camp;
//---
$code_lang_name  = $req['code_lang_name'] ?? "";
//---
if ($allow_whole_translate == '0') $tra_type = 'lead';
//---
// echo $_SERVER['SERVER_NAME'];
//---
$img_src = '//upload.wikimedia.org/wikipedia/commons/thumb/5/58/Wiki_Project_Med_Foundation_logo.svg/400px-Wiki_Project_Med_Foundation_logo.svg.png';
//---
$global_username = $GLOBALS['global_username'] ?? "";
// ---
$form_start1  = print_form_start1($allow_whole_translate, MainTables::$x_Langs_table, TablesSql::$s_campaign_input_list, $cat, $camp, $code_lang_name, $code, $tra_type, $global_username);
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
echo "<div class='container-fluid'>";
//---
$new_result = $_GET['new_result'] ?? "";
//---
if ($doit) {
    $data = [
        "camp" => $camp,
        "code" => $code,
        "code_lang_name" => $code_lang_name,
        "cat" => $cat,
        "tra_type" => $tra_type,
        "global_username" => $global_username,
        "filter_sparql" => $filter_sparql,
        "new_result" => $new_result,
        "test" => $test
    ];
    echo results_loader($data);
};
//---
echo "</div>";
//---
echo "<br>";
//---
include_once __DIR__ . '/footer.php';
//---
