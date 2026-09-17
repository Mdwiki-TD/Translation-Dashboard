<?PHP

namespace Results\GetResults2026;

use function Results\GetResults2026\get;
use function Results\GetResults2026\Tables\make_results_table_2026;
use function Results\GetResults2026\make_results_table_inprocess;
use function Results\GetResults2026\make_results_table_exists_2026;

use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function SQLorAPI\GetDataTab\get_td_or_sql_titles_infos;
use function SQLorAPI\GetDataTab\get_endpoint;

function results_loader_2026($data)
{
    // ---
    $camp        = $data["camp"];
    $code        = $data["code"];
    $cat         = $data["cat"];
    // ---
    $showExists = $data["show_exists"];
    // ---
    $global_username  = $data["global_username"];
    $inProgressBtn = $data["in_progress_translation_button"];
    // ---
    $full_translators = get_td_or_sql_full_translators();
    $full_translators = array_column($full_translators, 'is_active', 'user');

    $full_tr_user = ($full_translators[$global_username] ?? 0) == 1;

    $results_list = get($cat, $code);

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

    $_titles_infos = get_td_or_sql_titles_infos();
    $noLeadTranslates = load_translate_type('no');
    $fullTranslates = load_translate_type('full');
    $endpoint = get_endpoint();

    return Results_tables_2026(
        $tab,
        $showExists,
        $inProgressBtn,
        $full_tr_user,
        $_titles_infos,
        $noLeadTranslates,
        $fullTranslates,
        $endpoint
    );
}

function Results_tables_2026(
    $tab,
    $showExists,
    $inProgressBtn,
    $fullTrUser,
    $_titles_infos,
    $noLeadTranslates,
    $fullTranslates,
    $endpoint
) {

    $camp       = $tab["camp"];
    $code       = $tab["code"];
    $cat        = $tab["cat"];
    $traType   = $tab["tra_type"];
    $test       = $tab["test"];
    // ---
    $code_lang_name  = $tab["code_lang_name"];
    $globalUser = $tab["global_username"];
    $userCoord      = $tab["user_coord"];
    // ---
    $html = "";

    if (!empty($test)) {
        $html .= "code:$code<br>code_lang_name:$code_lang_name<br>";
    };

    $results = $tab["results_list"];

    $p_inprocess = $results['inprocess'];
    $missing     = $results['missing'];
    $ix          = $results['ix'];

    // { "title": "11p deletion syndrome", "category": "RTT", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 838, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153", "target": "متلازمة واجر" }
    $exists      = $results['exists'];

    $res_line = " Results: (" . count($results['missing']) . ")";

    if (!empty($test)) $res_line .= 'test:';

    $titlesInfos = array_column($_titles_infos, null, 'title');

    $table = make_results_table_2026(
        $missing,
        $code,
        $cat,
        $camp,
        $traType,
        $fullTrUser,
        $globalUser,
        $noLeadTranslates,
        $fullTranslates
    );

    $title_x = <<<HTML
        <!-- <span class='only_on_mobile'><b>Click the article name to translate</b></span> -->
        $ix
    HTML;

    $html .= card_result($res_line, $table, $title_x);

    $lenInProcess = count($p_inprocess);

    // ----- In-process table -----
    $lenInProcess = count($results['inprocess']);
    if ($lenInProcess > 0) {

        // $inProgressBtn = ($user_coord) ? $inProgressBtn : false;

        $inProcessTable = make_results_table_inprocess(
            $p_inprocess,
            $code,
            $cat,
            $camp,
            $inProgressBtn,
            $fullTrUser,
            $globalUser,
            $titlesInfos,
            $endpoint,
            $userCoord
        );

        $html .= card_result("In process: ($lenInProcess)", $inProcessTable);
    };

    $lenExists = count($exists);

    if ($lenExists > 1 && $showExists) {

        $table_3 = make_results_table_exists_2026(
            $exists,
            $code,
            $cat,
            $camp,
            $globalUser,
            $userCoord,
            $endpoint
        );

        $html .= card_result("Exists: ($lenExists)", $table_3);
    };

    return $html;
}
