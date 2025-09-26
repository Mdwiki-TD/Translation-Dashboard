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
use function Results\ResultsIndex\Results_tables;
//---
$allow_whole_translate = TablesSql::$s_settings['allow_type_of_translate']['value'] ?? '1';
//---
$req  = load_request();
// ---
$doit     = $req['doit'] ?? false;
$test     = $req['test'] ?? "";
$code     = $req['code'] ?? "";
$tra_type = $req['tra_type'] ?? "";
//---
$cat  = (!empty($req['cat'])) ? $req['cat'] : TablesSql::$s_main_cat;
$camp  = (!empty($req['camp'])) ? $req['camp'] : TablesSql::$s_main_camp;
//---
$code_lang_name  = $req['code_lang_name'] ?? "";
//---
if ($allow_whole_translate == '0') $tra_type = 'lead';
//---
$cat_ch = htmlspecialchars($cat, ENT_QUOTES);
$camp_ch = htmlspecialchars($camp, ENT_QUOTES);
//---
// echo $_SERVER['SERVER_NAME'];
//---
$img_src = '//upload.wikimedia.org/wikipedia/commons/thumb/5/58/Wiki_Project_Med_Foundation_logo.svg/400px-Wiki_Project_Med_Foundation_logo.svg.png';
//---
$form_start1  = print_form_start1($allow_whole_translate, MainTables::$x_Langs_table, TablesSql::$s_catinput_list, TablesSql::$s_campaign_input_list, $cat_ch, $camp_ch, $code_lang_name, $code, $tra_type);
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
if ($doit) {
    echo Results_tables($code, $camp, $cat, $tra_type, $code_lang_name, $test);
};
//---
echo "</div>";
//---
echo "<br>";
//---
include_once __DIR__ . '/footer.php';
//---
