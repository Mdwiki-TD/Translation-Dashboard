<?PHP

namespace Results\ResultsIndex;
//---
/*
Usage:

use function Results\ResultsIndex\Results_tables;
use function Results\ResultsIndex\results_loader;

*/

//---
use Tables\SqlTables\TablesSql;
use function Results\GetResults\get_results;
use function Results\GetResults\get_results_new;
use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function Results\ResultsTable\make_results_table;
use function Results\ResultsTableInprocess\make_results_table_inprocess;
use function Results\ResultsTableExists\make_results_table_exists;
use function TD\Render\admin_text;
use function Tables\SqlTables\load_translate_type;

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

function Results_tables($code, $camp, $cat, $tra_type, $code_lang_name, $global_username, $tab, $show_exists, $translation_button, $full_tr_user, $user_coord, $test)
{
    //---
    $html_result = "";
    //---
    if (!empty($test)) {
        $html_result .= "code:$code<br>code_lang_name:$code_lang_name<br>";
    };
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
    $mobile_td = $_GET["mobile_td"] ?? "1";
    //---
    $nolead_translates = load_translate_type('no');
    $translates_full = load_translate_type('full');
    //---
    $table = make_results_table($missing, $code, $cat, $camp, $tra_type, $full_tr_user, $global_username, $nolead_translates, $translates_full, $mobile_td);
    //---
    $title_x = <<<HTML
        <!-- <span class='only_on_mobile'><b>Click the article name to translate</b></span> -->
        $ix
    HTML;
    //---
    $html_result .= card_result($res_line, $table, $title_x);
    //---
    $len_inprocess = count($p_inprocess);
    //---
    if ($len_inprocess > 0) {
        //---
        $translation_button = ($user_coord) ? $translation_button : false;
        //---
        $table_2 = make_results_table_inprocess($p_inprocess, $code, $cat, $camp, $translation_button, $full_tr_user, $global_username, $mobile_td, $user_coord);
        //---
        $html_result .= card_result("In process: ($len_inprocess)", $table_2);
    };
    //---
    $len_exists = count($exists);
    //---
    if ($len_exists > 1 && $show_exists) {
        //---
        $table_3 = make_results_table_exists($exists, $code, $cat, $camp, $global_username, $user_coord);
        //---
        $html_result .= card_result("Exists: ($len_exists)", $table_3);
    };
    //---
    // $html_result .= '</div>';
    //---
    return $html_result;
}

function results_loader($tab)
{
    // ---
    $camp       = $tab["camp"];
    $code       = $tab["code"];
    $cat        = $tab["cat"];
    $tra_type   = $tab["tra_type"];
    $test       = $tab["test"];
    // ---
    $code_lang_name  = $tab["code_lang_name"];
    $global_username = $tab["global_username"];
    $filter_sparql   = $tab["filter_sparql"];
    $new_result      = $tab["new_result"];
    $user_coord      = $tab["user_coord"];
    // ---
    $depth  = TablesSql::$s_camp_input_depth[$camp] ?? 1;
    $cat2   = TablesSql::$s_camps_cat2[$camp] ?? '';
    // ---
    $user_in_coord = ($GLOBALS['user_in_coord'] ?? "") === true;
    //---
    $show_exists = ($user_in_coord || isset($_GET['exists']));
    //---
    $translation_button = TablesSql::$s_settings['translation_button_in_progress_table']['value'] ?? '0';
    //---
    if ($translation_button != "0") {
        $translation_button = $user_in_coord ? '1' : '0';
    };
    //---
    $full_translators = get_td_or_sql_full_translators();
    $full_translators = array_column($full_translators, 'active', 'user');
    //---
    // $full_tr_user = in_array($global_username, $full_translators);
    $full_tr_user = ($full_translators[$global_username] ?? 0) == 1;
    //---
    if ($new_result) {
        $tab = get_results_new($cat, $camp, $depth, $code, $filter_sparql, $cat2);
    } else {
        $tab = get_results($cat, $camp, $depth, $code, $filter_sparql, $cat2);
    }
    //---
    return Results_tables($code, $camp, $cat, $tra_type, $code_lang_name, $global_username, $tab, $show_exists, $translation_button, $full_tr_user, $user_coord, $test);
    //---
}
