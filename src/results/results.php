<?PHP

namespace Results\ResultsIndex;
//---
/*
Usage:

use function Results\ResultsIndex\Results_tables;

*/

//---
use Tables\SqlTables\TablesSql;
use function Results\GetResults\get_results;
use function Results\ResultsTable\make_results_table;
use function Results\ResultsTableExists\make_results_table_exists;
use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function TD\Render\admin_text;
use function SQLorAPI\Funcs\get_lang_pages_by_cat;

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

function Results_tables($code, $camp, $cat, $tra_type, $code_lang_name, $test)
{
    //---
    $depth  = TablesSql::$s_camp_input_depth[$camp] ?? 1;
    //---
    $translation_button = TablesSql::$s_settings['translation_button_in_progress_table']['value'] ?? '0';
    //---
    if ($translation_button != "0") {
        $translation_button = (($GLOBALS['user_in_coord'] ?? "") === true) ? '1' : '0';
    };
    //---
    if (!empty($test)) {
        echo "code:$code<br>code_lang_name:$code_lang_name<br>";
    };
    //---
    $tab = get_results($cat, $camp, $depth, $code);
    //---
    $p_inprocess = $tab['inprocess'];
    $missing     = $tab['missing'];
    $ix          = admin_text($tab['ix']);
    //---
    $exists      = $tab['exists'];
    //---
    $res_line = " Results: (" . count($tab['missing']) . ")";
    //---
    if (!empty($test)) $res_line .= 'test:';
    //---
    $full_translators = get_td_or_sql_full_translators('user');
    $full_tr_user = in_array($GLOBALS['global_username'], $full_translators);
    //---
    $table = make_results_table($missing, $code, $cat, $camp, $tra_type, $translation_button, $full_tr_user);
    //---
    $title_x = <<<HTML
            <span class='only_on_mobile'><b>Click the article name to translate</b></span>
            $ix
        HTML;
    //---
    echo card_result($res_line, $table, $title_x);
    //---
    $len_inprocess = count($p_inprocess);
    //---
    if ($len_inprocess > 0) {
        //---
        $table_2 = make_results_table($p_inprocess, $code, $cat, $camp, $tra_type, $translation_button, $full_tr_user, $inprocess = true);
        //---
        echo card_result("In process: ($len_inprocess)", $table_2);
    };
    //---
    $user_in_coord = ($GLOBALS['user_in_coord'] ?? "") === true;
    //---
    $len_exists = count($exists);
    //---
    if ($len_exists > 1 && $user_in_coord) {
        //---
        $exists_targets = get_lang_pages_by_cat($code, $cat);
        //---
        $exists_targets_before = [];
        //---
        $table_3 = make_results_table_exists($exists, $code, $cat, $camp, $translation_button, $full_tr_user, $exists_targets, $exists_targets_before);
        //---
        echo card_result("Exists: ($len_exists)", $table_3);
    };
    //---
    echo '</div>';

    return "";
}
