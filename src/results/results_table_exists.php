<?PHP

namespace Results\ResultsTableExists;

/*
Usage:

use function Results\ResultsTableExists\make_results_table_exists;

*/

use Tables\Main\MainTables;

use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function Results\TrLink\make_ContentTranslation_url;
use function TD\Render\Html\make_mdwiki_article_url_blank;
use function TD\Render\Html\make_wikipedia_url_blank;
use function TD\Render\Html\make_wikidata_url_blank;

function one_item_props($title, $target)
{

    $sql_qids = get_td_or_sql_qids();
    //---
    $word  = MainTables::$x_Words_table[$title] ?? 0;
    $refs  = MainTables::$x_Lead_Refs_table[$title] ?? 0;
    $asse  = MainTables::$x_Assessments_table[$title] ?? '';
    $views = MainTables::$x_enwiki_pageviews_table[$title] ?? 0;
    $qid   = $sql_qids[$title] ?? "";
    //---
    if (empty($asse)) $asse = 'Unknown';
    //---
    $tab = [
        'word'  => $word,
        'refs'  => $refs,
        'asse'  => $asse,
        'views' => $views,
        'qid'   => $qid,
        'target' => $target
    ];
    //---
    return $tab;
}

function make_one_row_new($title, $cnt, $langcode, $cat, $camp, $props, $global_username, $user_coord)
{
    //---
    $words = $props['word'];
    $refs  = $props['refs'];
    $asse  = $props['asse'];
    $pageviews = $props['views'];
    $qid = $props['qid'];
    //---
    $mdwiki_a_tag = make_mdwiki_article_url_blank($title);
    //---
    $qid_url = make_wikidata_url_blank($qid);
    //---
    $target = $props['target'] ?? '';
    $target_before = $props['target_before'] ?? '';
    //---
    $target_tab = "";
    $target_tab2 = "";
    //---
    if (!empty($target)) {
        // ---
        $target_tab = make_wikipedia_url_blank($target, $langcode);
    } elseif (!empty($target_before)) {
        // ---
        $target_tab2 = make_wikipedia_url_blank($target_before, $langcode);
    }
    //---
    $translate_url = make_ContentTranslation_url($title, $langcode, $cat, $camp, 'lead');
    //---
    $tab = ($global_username !== "" && $user_coord) ? <<<HTML
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
            $asse
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

function make_results_table_exists($items, $langcode, $cat, $camp, $global_username, $user_coord)
{
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
        // $target_td = $exists_targets[$title] ?? null;
        // $target_before = $exists_targets_before[$title] ?? null;
        //---
        $target_td = ($target_tab["via"] === "td") ? $target_tab["target"] : null;
        $target_before = ($target_tab["via"] !== "td") ? $target_tab["target"] : null;
        //---
        $count_translated += !empty($target_td);
        //---
        $count_translated_before += empty($target_td) && !empty($target_before);
        //---
        $props = one_item_props($title, $target_td);
        $props["target_before"] = $target_before;
        //---
        $row = make_one_row_new($title, $cnt, $langcode, $cat, $camp, $props, $global_username, $user_coord);
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
        <table class="table compact table-striped table_100 table_text_left table_responsive_main display">
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
