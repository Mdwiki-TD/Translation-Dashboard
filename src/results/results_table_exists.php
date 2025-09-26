<?PHP

namespace Results\ResultsTableExists;

/*
Usage:

use function Results\ResultsTableExists\make_results_table_exists;

*/

use Tables\Main\MainTables;

use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function Results\ResultsTable\Rows\make_translate_urls;
use function Results\TrLink\make_translate_link_medwiki;

function sort_py_PageViews($items, $en_views_tab)
{
    $dd = [];
    foreach ($items as $t) {
        $t = str_replace('_', ' ', $t);
        $kry = $en_views_tab[$t] ?? 0;
        $dd[$t] = $kry;
    }
    arsort($dd);
    return $dd;
}

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

function make_one_row_new($title, $cnt, $langcode, $cat, $camp, $tra_btn, $full_tr_user, $props)
{
    //---
    $tra_type = "lead";
    //---
    if (strtolower(substr($title, 0, 6)) == 'video:') {
        $tra_type = 'all';
    };
    //---
    $words = $props['word'];
    $refs  = $props['refs'];
    $asse  = $props['asse'];
    $pageviews = $props['views'];
    $qid = $props['qid'];
    //---
    $title2 = rawurlEncode($title);
    $mdwiki_url = "//mdwiki.org/wiki/" . str_replace('+', '_', $title2);
    //---
    $qid_url = (!empty($qid)) ? "<a class='inline' target='_blank' href='https://wikidata.org/wiki/$qid'>$qid</a>" : '&nbsp;';
    //---
    $target = $props['target'] ?? '';
    $target_before = $props['target_before'] ?? '';
    //---
    $target_tab = "";
    $target_tab2 = "";
    $target_url = "";
    //---
    if (!empty($target)) {
        $encoded_target = rawurlencode(str_replace(' ', '_', $target));
        $target_url = "https://$langcode.wikipedia.org/wiki/$encoded_target";
        $target_tab = "<a target='_blank' href='$target_url'>$target</a>";
    } elseif (!empty($target_before)) {
        $encoded_target2 = rawurlencode(str_replace(' ', '_', $target_before));
        $target_url2 = "https://$langcode.wikipedia.org/wiki/$encoded_target2";
        $target_tab2 = "<a target='_blank' href='$target_url2'>$target_before</a>";
    }
    //---
    // [$tab, $translate_url, $full_translate_url] = make_translate_urls($title, $tra_type, $props['word'], $langcode, $cat, $camp, "", $mdwiki_url, $tra_btn, "", $full_tr_user);
    //---
    $translate_url = make_translate_link_medwiki($title, $langcode, $cat, $camp, 'lead');
    //---
    $tab = <<<HTML
        <div class='inline'>
            <a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Translate</a>
        </div>
    HTML;
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
            <a target='_blank' href='$mdwiki_url'>$title</a>
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

function make_results_table_exists($items, $langcode, $cat, $camp, $tra_btn, $full_tr_user, $exists_targets, $exists_targets_before)
{
    //---
    $dd = [];
    $dd = sort_py_PageViews($items, MainTables::$x_enwiki_pageviews_table);
    //---
    $list = "";
    //---
    $cnt = 1;
    $count_translated = 0;
    $count_translated_before = 0;
    //---
    $exists_targets = array_column($exists_targets, 'target', 'title');
    $exists_targets_before = array_column($exists_targets_before, 'target', 'title');
    //---
    foreach ($dd as $v => $gt) {
        // ---
        if (empty($v)) continue;
        // ---
        $title = str_replace('_', ' ', $v);
        //---
        $target = $exists_targets[$title] ?? null;
        //---
        $target_before = $exists_targets_before[$title] ?? null;
        //---
        $count_translated += !empty($target);
        //---
        $count_translated_before += empty($target) && !empty($target_before);
        //---
        $props = one_item_props($title, $target);
        $props["target_before"] = $target_before;
        //---
        $row = make_one_row_new($title, $cnt, $langcode, $cat, $camp, $tra_btn, $full_tr_user, $props);
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
