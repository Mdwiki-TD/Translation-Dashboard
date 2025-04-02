<?PHP

namespace Results\ResultsTableExists;

/*
Usage:

use function Results\ResultsTableExists\make_results_table_exists;

*/

include_once __DIR__ . '/../Tables/include.php';

use Tables\Main\MainTables;

use function SQLorAPI\GetDataTab\get_td_or_sql_qids;

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

function one_p_props($title)
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
        'qid'   => $qid
    ];
    //---
    return $tab;
}
function d_one_row($title, $cnt, $cod)
{
    //---
    $props = one_p_props($title);
    //---
    $words = $props['word'];
    $refs  = $props['refs'];
    $asse  = $props['asse'];
    $pageviews = $props['views'];
    $qid = $props['qid'];
    //---
    $title2 = rawurlEncode($title);
    $mdwiki_url = "//mdwiki.org/wiki/" . str_replace('+', '_', $title2);
    $qid = (!empty($qid)) ? "<a class='inline' target='_blank' href='https://wikidata.org/wiki/$qid'>$qid</a>" : '&nbsp;';
    //---
    $target = $props['target'] ?? '';
    //---
    $target_tab = "";
    $target_url = "";
    //---
    if (!empty($target)) {
        $encoded_target = rawurlencode(str_replace(' ', '_', $target));
        $target_url = "https://$cod.wikipedia.org/wiki/$encoded_target";
        $target_tab = "<a target='_blank' href='$target_url'>$target</a>";
    }
    //---
    $td_rows = <<<HTML
        <tr>
            <th class='' scope="row"  style="text-align: center">$cnt</th>
            <td class='link_container spannowrap'><a target='_blank' href='$mdwiki_url'>$title</a></td>
            <td><span>$target_tab</span></td>
            <td class='num' style="text-align: left">$pageviews</td>
            <td class='num' style="text-align: left">$asse</td>
            <td class='num' style="text-align: left">$words</td>
            <td class='num' style="text-align: left">$refs</td>
            <td>$qid</td>
        </tr>
    HTML;
    //---
    return $td_rows;
}

function make_results_table_exists($items, $cod, $cat, $camp)
{
    //---
    $frist = <<<HTML
        <table class="table display compact table-striped table_responsive">
            <thead>
                <tr>
                    <th class="all">
                        #
                    </th>
                    <th class="spannowrap all" data-priority="1">
                        Title
                    </th>
                    <th class="not-mobile" data-priority="2">
                        Translated
                    </th>
                    <th class="spannowrap not-mobile" style="text-align: center">
                        <span data-bs-toggle="tooltip" data-bs-title="Page views in last month in English Wikipedia">Views</span>
                    </th>
                    <th class="spannowrap not-mobile" style="text-align: center">
                        <span data-bs-toggle="tooltip" data-bs-title="Page important from medicine project in English Wikipedia">Importance</span>
                    </th>
                    <th class="spannowrap not-mobile" style="text-align: center">
                        <span data-bs-toggle="tooltip" data-bs-title="number of words of the article in mdwiki.org">Words</span>
                    </th>
                    <th class="spannowrap not-mobile" style="text-align: center">
                        <span data-bs-toggle="tooltip" data-bs-title="number of references of the article in mdwiki.org">Refs.</span>
                    </th>
                    <th class="spannowrap not-mobile" style="text-align: center">
                        <span data-bs-toggle="tooltip" data-bs-title="Wikidata identifier">Qid</span>
                    </th>
                </tr>
            </thead>
            <tbody>
    HTML;
    //---
    $dd = [];
    $dd = sort_py_PageViews($items, MainTables::$x_enwiki_pageviews_table);
    //---
    $list = "";
    $cnt = 1;
    //---
    foreach ($dd as $v => $gt) {
        // ---
        if (empty($v)) continue;
        // ---
        $title = str_replace('_', ' ', $v);
        //---
        $row = d_one_row($title, $cnt, $cod);
        //---
        $list .= $row;
        //---
        $cnt++;
    };
    // ---
    $last = <<<HTML
        </tbody>
    </table>
    HTML;
    // ---
    return $frist . $list . $last;
}
