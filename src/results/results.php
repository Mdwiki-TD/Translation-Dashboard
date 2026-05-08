<?PHP

namespace Results\ResultsIndex;
//---
/*
Usage:

use function Results\ResultsIndex\Results_tables;
use function Results\ResultsIndex\results_loader;

*/

//---
use function Results\GetResults\get_results;
use function Results\GetResults\get_results_new;
use function Results\ResultsTable\make_results_table;
use function Results\ResultsTableInprocess\make_results_table_inprocess;
use function Results\ResultsTableExists\make_results_table_exists;
use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function TD\Render\admin_text;
use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;
use function SQLorAPI\GetDataTab\get_td_or_sql_titles_infos;

function load_translate_type($ty)
{
    static $full_translates = [];
    static $no_lead_translates = [];

    if (empty($full_translates)) {
        $rere = get_td_or_sql_translate_type();
        //---
        foreach ($rere as $k => $tab) {
            // if tt_full == 1 then add tt_title to $full_translates
            if ($tab['tt_full'] == 1) {
                $full_translates[] = $tab['tt_title'];
            }
            if ($tab['tt_lead'] == 0) {
                $no_lead_translates[] = $tab['tt_title'];
            }
        }
    }
    // ---
    $tab = ($ty == 'full') ? $full_translates : $no_lead_translates;
    // ---
    return $tab;
}
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

function Results_tables(
    $tab,
    $show_exists,
    $translation_button,
    $full_tr_user,
    $titles_infos,
    $nolead_translates,
    $translates_full
) {
    //---
    $camp       = $tab["camp"];
    $code       = $tab["code"];
    $cat        = $tab["cat"];
    $tra_type   = $tab["tra_type"];
    $test       = $tab["test"];
    // ---
    $code_lang_name  = $tab["code_lang_name"];
    $global_username = $tab["global_username"];
    $user_coord      = $tab["user_coord"];
    // ---
    $html_result = "";
    //---
    if (!empty($test)) {
        $html_result .= "code:$code<br>code_lang_name:$code_lang_name<br>";
    };
    //---
    $results_list = $tab["results_list"];
    //---
    $p_inprocess = $results_list['inprocess'];
    $missing     = $results_list['missing'];
    $ix          = admin_text($results_list['ix']);
    //---
    $exists      = $results_list['exists'];
    //---
    $res_line = " Results: (" . count($results_list['missing']) . ")";
    //---
    if (!empty($test)) $res_line .= 'test:';
    //---
    $titles_infos_items = array_column($titles_infos, null, 'title');
    //---
    $table = make_results_table(
        $missing,
        $code,
        $cat,
        $camp,
        $tra_type,
        $full_tr_user,
        $global_username,
        $nolead_translates,
        $translates_full,
        $titles_infos
    );
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
        $table_2 = make_results_table_inprocess(
            $p_inprocess,
            $code,
            $cat,
            $camp,
            $translation_button,
            $full_tr_user,
            $global_username,
            $titles_infos_items
        );
        //---
        $html_result .= card_result("In process: ($len_inprocess)", $table_2);
    };
    //---
    $len_exists = count($exists);
    //---
    if ($len_exists > 1 && $show_exists) {
        //---
        $table_3 = make_results_table_exists(
            $exists,
            $code,
            $cat,
            $camp,
            $global_username,
            $user_coord,
            $titles_infos_items
        );
        //---
        $html_result .= card_result("Exists: ($len_exists)", $table_3);
    };
    //---
    // $html_result .= '</div>';
    //---
    return $html_result;
}

function results_loader($data)
{
    // ---
    $camp        = $data["camp"];
    $code        = $data["code"];
    $cat         = $data["cat"];
    $depth       = $data["depth"];
    // ---
    $show_exists = $data["show_exists"];
    $category2 = $data["category2"];
    // ---
    $global_username  = $data["global_username"];
    $filter_sparql    = $data["filter_sparql"];
    $new_result       = $data["new_result"];
    $translate_button = $data["translation_button"];
    // ---
    $full_translators = get_td_or_sql_full_translators();
    $full_translators = array_column($full_translators, 'is_active', 'user');
    //---
    // $full_tr_user = in_array($global_username, $full_translators);
    $full_tr_user = ($full_translators[$global_username] ?? 0) == 1;
    //---
    if ($new_result) {
        $results_list = get_results_new($cat, $camp, $depth, $code, $filter_sparql, $category2);
    } else {
        $results_list = get_results($cat, $camp, $depth, $code, $filter_sparql, $category2);
    }
    //---
    $tab = [
        "code" => $code,
        "camp" => $camp,
        "cat" => $cat,
        "tra_type" => $data["tra_type"],
        "code_lang_name" => $data["code_lang_name"],
        "global_username" => $global_username,
        "results_list" => $results_list,
        "user_coord" => $data["user_coord"],
        "test" => $data["test"]
    ];
    //---
    $titles_infos = get_td_or_sql_titles_infos();
    $nolead_translates = load_translate_type('no');
    $translates_full = load_translate_type('full');
    //---
    return Results_tables(
        $tab,
        $show_exists,
        $translate_button,
        $full_tr_user,
        $titles_infos,
        $nolead_translates,
        $translates_full
    );
}
