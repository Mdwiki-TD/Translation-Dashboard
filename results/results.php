<?PHP

namespace Results\Results;
//---
include_once __DIR__ . '/include.php';
//---
use Tables\SqlTables\TablesSql;
use function Results\GetResults\get_results;
use function Results\ResultsTable\make_results_table;
use function Results\ResultsTableExists\make_results_table_exists;
use function Actions\LoadRequest\load_request;
//---
$doit = filter_input(INPUT_GET, 'doit', FILTER_VALIDATE_BOOL) ?? false;

$tra_type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

$depth = filter_input(INPUT_GET, 'depth', FILTER_VALIDATE_INT, [
    'options' => ['default' => 1, 'min_range' => 1, 'max_range' => 10] // عدّل max حسب حاجتك
]);
//---
$req  = load_request();
$code = $req['code'] ?? "";
$camp  = $req['camp'] ?? "";
$cat  = $req['cat'] ?? "";
$code_lang_name = $req['code_lang_name'] ?? "";
//---
$translation_button = TablesSql::$s_settings['translation_button_in_progress_table']['value'] ?? '0';
//---
if ($translation_button != "0") {
    $translation_button = ($GLOBALS['user_in_coord'] === true) ? '1' : '0';
};
//---
$depth  = TablesSql::$s_camp_input_depth[$camp] ?? 1;
//---
if (empty($code_lang_name)) $doit = false;
//---
echo "<div class='container-fluid'>";
//---
function card_result($title, $text, $title2 = "")
{
    return <<<HTML
    <br>
    <div class='card'>
        <div class="card-header">
            <span class="card-title h5">
                $title
            </span>
            $title2
            <div class="card-tools">
                <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class='card-body1 card2'>
            $text
        </div>
    </div>
    HTML;
}
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
    //---
    $exists     = $tab['exists'];
    //---
    $res_line = " Results: (" . count($tab['missing']) . ")";
    //---
    if (isset($_GET['test'])) $res_line .= 'test:';
    //---
    $table = make_results_table($missing, $code, $cat, $camp, $tra_type, $translation_button);
    //---
    $title_x = <<<HTML
        <span class='only_on_mobile'><b>Click the article name to translate</b></span>
        <!-- $ix -->
    HTML;
    //---
    echo card_result($res_line, $table, $title_x);
    //---
    $len_inprocess = count($p_inprocess);
    //---
    if ($len_inprocess > 0) {
        //---
        $table_2 = make_results_table($p_inprocess, $code, $cat, $camp, $tra_type, $translation_button, $inprocess = true);
        //---
        echo card_result("In process: ($len_inprocess)", $table_2);
    };
    //---
    $len_exists = count($exists);
    //---
    if ($len_exists > 5000) {
        //---
        $table_3 = make_results_table_exists($exists, $code, $cat, $camp);
        //---
        echo card_result("Exists: ($len_exists)", $table_3);
    };
    //---
    echo '</div>';
};
//---
echo "</div>";
