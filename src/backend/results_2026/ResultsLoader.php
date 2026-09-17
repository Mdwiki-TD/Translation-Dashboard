<?PHP

namespace Results\GetResults2026;

use function Results\GetResults2026\get;
use function Results\GetResults2026\Tables\make_results_table_2026;
use function Results\GetResults2026\make_results_table_inprocess;
use function Results\GetResults2026\make_results_table_exists_2026;

use Results\GetResults2026\Helpers\TranslateTypeLoader;
use function Results\GetResults2026\Helpers\render;

use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function SQLorAPI\GetDataTab\get_td_or_sql_titles_infos;
use function SQLorAPI\GetDataTab\get_endpoint;

function results_loader_2026($data)
{

    $camp        = $data["camp"];
    $code        = $data["code"];
    $cat         = $data["cat"];

    $showExists = $data["show_exists"];

    $globalUsername  = $data["global_username"];
    $inProgressButton = $data["in_progress_translation_button"];

    $fullTranslators = get_td_or_sql_full_translators();
    $fullTranslators = array_column($fullTranslators, 'is_active', 'user');

    $fullTrUser = ($fullTranslators[$globalUsername] ?? 0) == 1;

    $resultsList = get($cat, $code);

    $tab = [
        "code" => $code,
        "camp" => $camp,
        "cat" => $cat,
        "tra_type" => $data["tra_type"],
        "code_lang_name" => $data["code_lang_name"],
        "global_username" => $globalUsername,
        "results_list" => $resultsList,
        "user_coord" => $data["user_coord"],
        "test" => $data["test"]
    ];

    $_titles_infos = get_td_or_sql_titles_infos();
    $noLeadTranslates = TranslateTypeLoader::load('no');
    $fullTranslates = TranslateTypeLoader::load('full');
    $endpoint = get_endpoint();

    return Results_tables_2026(
        $tab,
        $showExists,
        $inProgressButton,
        $fullTrUser,
        $_titles_infos,
        $noLeadTranslates,
        $fullTranslates,
        $endpoint
    );
}

function Results_tables_2026(
    $tab,
    $showExists,
    $inProgressButton,
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

    $codeLangName  = $tab["code_lang_name"];
    $globalUser = $tab["global_username"];
    $userCoord      = $tab["user_coord"];

    $html = "";

    if (!empty($test)) {
        $html .= "code:$code<br>code_lang_name:$codeLangName<br>";
    };

    $results = $tab["results_list"];

    $pInprocess = $results['inprocess'];
    $missing     = $results['missing'];
    $ix          = $results['ix'];

    // { "title": "11p deletion syndrome", "category": "RTT", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 838, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153", "target": "متلازمة واجر" }
    $exists      = $results['exists'];

    $resLine = " Results: (" . count($results['missing']) . ")";

    if (!empty($test)) $resLine .= 'test:';

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

    $html .= render($resLine, $table, $title_x);

    $lenInProcess = count($pInprocess);

    // ----- In-process table -----
    $lenInProcess = count($results['inprocess']);
    if ($lenInProcess > 0) {

        // $inProgressButton = ($userCoord) ? $inProgressButton : false;

        $inProcessTable = make_results_table_inprocess(
            $pInprocess,
            $code,
            $cat,
            $camp,
            $inProgressButton,
            $fullTrUser,
            $globalUser,
            $titlesInfos,
            $endpoint,
            $userCoord
        );

        $html .= render("In process: ($lenInProcess)", $inProcessTable);
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

        $html .= render("Exists: ($lenExists)", $table_3);
    };

    return $html;
}
