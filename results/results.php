<?PHP

namespace Results\Results;
//---
include_once __DIR__ . '/include.php';
//---
use function Results\GetResults\get_results;
use function Results\ResultsTable\make_results_table;
use function Actions\LoadRequest\load_request;
//---
$doit = isset($_REQUEST['doit']);
//---
$tra_type  = $_REQUEST['type'] ?? '';
//---
$req  = load_request();
$code = $req['code'] ?? "";
$camp  = $req['camp'] ?? "";
$cat  = $req['cat'] ?? "";
$code_lang_name = $req['code_lang_name'] ?? "";
//---
$translation_button = $settings['translation_button_in_progress_table']['value'] ?? '0';
//---
if ($translation_button != "0" && $GLOBALS['user_in_coord'] === true) $translation_button = '1';
//---
$depth  = $_REQUEST['depth'] ?? 1;
$depth  = $depth * 1;
//---
$depth  = TablesSql::$s_camp_input_depth[$camp] ?? 1;
//---
if (empty($code_lang_name)) $doit = false;
//---
echo "<div class='container-fluid'>";
//---
if ($doit) {
    //---
    if (isset($_GET['test'])) {
        echo "code:$code<br>code_lang_name:$code_lang_name<br>";
    };
    //---
    $tab = get_results($cat, $camp, $depth, $code);
    //---
    $p_inprocess = $tab['inprocess'];
    $missing    = $tab['missing'];
    $ix         = $tab['ix'];
    $len_inprocess = count($p_inprocess);
    //---
    $res_line = " Results (" . count($tab['missing']) . ")";
    //---
    if (isset($_GET['test'])) $res_line .= 'test:';
    //---
    $table = make_results_table($missing, $code, $cat, $camp, $tra_type, $translation_button);
    //---
    echo <<<HTML
    <br>
    <div class='card'>
        <div class='card-header'>
            <span class='h5'>$res_line:</span> <span class='only_on_mobile'><b>Click the article name to translate</b></span>
            <!-- <h5>$ix</h5> -->
        </div>
        <div class='card-body1 card2'>
            $table
        </div>
    </div>
    HTML;
    //---
    if ($len_inprocess > 0) {
        //---
        $table_2 = make_results_table($p_inprocess, $code, $cat, $camp, $tra_type, $translation_button, $inprocess = true);
        //---
        echo <<<HTML
        <br>
        <div class='card'>
            <div class='card-header'>
                <h5>In process ($len_inprocess):</h5>
            </div>
            <div class='card-body1 card2'>
                $table_2
            </div>
        </div>
        HTML;
    };
    //---
    echo '</div>';
};
//---
echo "</div>";
