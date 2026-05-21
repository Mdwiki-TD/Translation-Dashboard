<?PHP

namespace Results\GetResults2026;

/*
Usage:

use function Results\GetResults2026\make_results_table_exists;

*/

use function Results\TrLink\make_ContentTranslation_url;
use function TD\Render\Html\make_mdwiki_article_url_blank;
use function TD\Render\Html\make_wikipedia_url_blank;
use function TD\Render\Html\make_wikidata_url_blank;

function make_one_row_exists(
    $title,
    $cnt,
    $langcode,
    $cat,
    $camp,
    $title_data,
    $global_username,
    $user_coord,
    $endpoint
) {
    //---
    // target_tab = { "title": "11p deletion syndrome", "category": "RTT", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 838, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153", "target": "متلازمة واجر" , "via":td" }
    //---
    $importance  = $title_data['importance'] ?? "Unknown";
    //---
    $words = $title_data['w_lead_words'] ?? 0;
    $refs  = $title_data['r_lead_refs'] ?? 0;
    $pageviews = $title_data['en_views'] ?? 0;
    $qid = $title_data['qid'] ?? "";
    //---
    $mdwiki_a_tag = make_mdwiki_article_url_blank($title);
    //---
    $qid_url = make_wikidata_url_blank($qid);
    //---
    $target_tab = "";
    $target_tab2 = "";
    //---
    if ($title_data['target']) {
        if ($title_data["via"] === "td") {
            $target_tab = make_wikipedia_url_blank($title_data['target'], $langcode);
        } else {
            $target_tab2 = make_wikipedia_url_blank($title_data['target'], $langcode);
        }
    }
    //---
    $translate_url = make_ContentTranslation_url(
        $title,
        $langcode,
        $cat,
        $camp,
        'lead',
        $endpoint
    );
    //---
    $tab = (!empty($global_username) && $user_coord) ? <<<HTML
        <div class='inline'>
            <a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Translate</a>
        </div>
    HTML : "";
    //---
    $td22 = <<<HTML
            <td class='num'>
            $pageviews
        </td>
        <td class='num'>
            $importance
        </td>
        <td class='num'>
            $words
        </td>
        <td class='num'>
            $refs
        </td>
    HTML;
    //---
    $td22 = "";
    //---
    $td_rows = <<<HTML
        <th class='' scope="row" style="text-align: center">
            $cnt
        </th>
        <td class='link_container spannowrap'>
            $mdwiki_a_tag
        </td>
        <td>
            $tab
        </td>
        <td>
            $target_tab
        </td>
        <td>
            $target_tab2
        </td>
        $td22
        <td>
            $qid_url
        </td>
    HTML;
    //---
    $td_rows = "<tr class=''>$td_rows</tr>";
    //---
    return $td_rows;
}

function make_results_table_exists(
    $items,
    $langcode,
    $cat,
    $camp,
    $global_username,
    $user_coord,
    $endpoint
) {
    //---
    $list = "";
    //---
    $cnt = 1;
    $count_translated = 0;
    $count_translated_before = 0;
    //---
    foreach ($items as $title => $target_tab) {
        // ---
        if (empty($title)) continue;
        // ---
        $title = str_replace('_', ' ', $title);
        //---
        if ($target_tab["via"] === "td") {
            $count_translated += 1;
        } else {
            $count_translated_before += 1;
        }
        //---
        $row = make_one_row_exists(
            $title,
            $cnt,
            $langcode,
            $cat,
            $camp,
            $target_tab,
            $global_username,
            $user_coord,
            $endpoint
        );
        //---
        $list .= $row;
        //---
        $cnt++;
    };
    // ---
    $th22 = <<<HTML
        <th class="spannowrap" style="text-align: center">
            <span data-bs-toggle="tooltip" data-bs-title="Page views in last month in English Wikipedia">Views</span>
        </th>
        <th class="spannowrap" style="text-align: center">
            <span data-bs-toggle="tooltip" data-bs-title="Page important from medicine project in English Wikipedia">Importance</span>
        </th>
        <th class="spannowrap" style="text-align: center">
            <span data-bs-toggle="tooltip" data-bs-title="number of words of the article in mdwiki.org">Words</span>
        </th>
        <th class="spannowrap" style="text-align: center">
            <span data-bs-toggle="tooltip" data-bs-title="number of references of the article in mdwiki.org">Refs.</span>
        </th>
    HTML;
    // ---
    $th22 = "";
    // ---
    $table = <<<HTML
        <table class="table compact table-striped table_100 table_text_left table_responsive display">
            <thead>
                <tr>
                    <th class="num">
                        #
                    </th>
                    <th class="spannowrap" style="text-align: center">
                        Title
                    </th>
                    <th class="">
                        <span class=''>Translate</span>
                    </th>
                    <th class="">
                        Translated ($count_translated)
                    </th>
                    <th class="">
                        Translated before ($count_translated_before)
                    </th>
                    $th22
                    <th class="spannowrap" style="text-align: center">
                        <span data-bs-toggle="tooltip" data-bs-title="Wikidata identifier">Qid</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                $list
            </tbody>
        </table>
    HTML;
    // ---
    return $table;
}
