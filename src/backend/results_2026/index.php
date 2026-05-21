<?PHP

namespace Results\GetResults2026;
//---
/*
Usage:

use function Results\GetResults2026\results_loader;

*/

//---x
use function Results\GetResults2026\get_results_2026;
use function Results\GetResults2026\make_results_table_2026;
use function Results\GetResults2026\make_results_table_inprocess;
use function Results\GetResults2026\make_results_table_exists_2026;

use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;
use function SQLorAPI\GetDataTab\get_td_or_sql_titles_infos;
use function SQLorAPI\GetDataTab\get_endpoint;

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

function Results_tables_2026(
    $tab,
    $show_exists,
    $translation_button,
    $full_tr_user,
    $titles_infos,
    $nolead_translates,
    $translates_full,
    $endpoint
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
    $ix          = $results_list['ix'];
    //---
    // { "title": "11p deletion syndrome", "category": "RTT", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 838, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153", "target": "متلازمة واجر" }
    $exists      = $results_list['exists'];
    //---
    $res_line = " Results: (" . count($results_list['missing']) . ")";
    //---
    if (!empty($test)) $res_line .= 'test:';
    //---
    $titles_infos_items = array_column($titles_infos, null, 'title');
    //---
    $table = make_results_table_2026(
        $missing,
        $code,
        $cat,
        $camp,
        $tra_type,
        $full_tr_user,
        $global_username,
        $nolead_translates,
        $translates_full
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
            $titles_infos_items,
            $endpoint
        );
        //---
        $html_result .= card_result("In process: ($len_inprocess)", $table_2);
    };
    //---
    $len_exists = count($exists);
    //---
    if ($len_exists > 1 && $show_exists) {
        //---
        $table_3 = make_results_table_exists_2026(
            $exists,
            $code,
            $cat,
            $camp,
            $global_username,
            $user_coord,
            $endpoint
        );
        //---
        $html_result .= card_result("Exists: ($len_exists)", $table_3);
    };
    //---
    // $html_result .= '</div>';
    //---
    return $html_result;
}

function results_loader_2026($data)
{
    // ---
    $camp        = $data["camp"];
    $code        = $data["code"];
    $cat         = $data["cat"];
    // ---
    $show_exists = $data["show_exists"];
    // ---
    $global_username  = $data["global_username"];
    $translate_button = $data["translation_button"];
    // ---
    $full_translators = get_td_or_sql_full_translators();
    $full_translators = array_column($full_translators, 'is_active', 'user');
    //---
    $full_tr_user = ($full_translators[$global_username] ?? 0) == 1;
    //---
    $results_list = get_results_2026($cat, $code);
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
    $endpoint = get_endpoint();
    //---
    return Results_tables_2026(
        $tab,
        $show_exists,
        $translate_button,
        $full_tr_user,
        $titles_infos,
        $nolead_translates,
        $translates_full,
        $endpoint
    );
}
