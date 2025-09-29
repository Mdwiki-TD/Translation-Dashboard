<?PHP

namespace Results\ResultsTable;

/*
Usage:

use function Results\ResultsTable\sort_py_PageViews;
use function Results\ResultsTable\sort_py_importance;
use function Results\ResultsTable\make_one_row;
use function Results\ResultsTable\make_results_table;
use function Results\ResultsTable\normalizeItems;

*/

use Tables\Main\MainTables;

use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function Results\ResultsTable\Rows\make_td_rows_responsive;
use function Results\ResultsTable\Rows\make_td_rows_mobile;
use function Results\ResultsTable\Rows\make_mobile_table;
use function Results\ResultsTable\Rows\make_translate_urls;
use function TD\Render\Html\make_mdwiki_href;
use function TD\Render\Html\make_wikidata_url_blank;

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

function sort_py_importance($items, $Assessment_table)
{
    // ---
    $Assessment_fff = [
        'Top' => 1,
        'High' => 2,
        'Mid' => 3,
        'Low' => 4,
        'Unknown' => 5,
        '' => 5
    ];
    // ---
    $empty = $Assessment_fff['Unknown'] ?? '';
    $dd = [];
    foreach ($items as $t) {
        $t = str_replace('_', ' ', $t);
        $aa = $Assessment_table[$t] ?? null;
        if (isset($aa)) {
            $kry = $Assessment_fff[$aa] ?? $empty;
        }
        $dd[$t] = $kry;
    }
    arsort($dd);
    return $dd;
}

function one_item_props($title, $langcode, $tra_type)
{

    $words_tab = ($tra_type == 'all') ? MainTables::$x_All_Words_table : MainTables::$x_Words_table;
    $ref_tab   = ($tra_type == 'all') ? MainTables::$x_All_Refs_table  : MainTables::$x_Lead_Refs_table;
    //---
    $sql_qids = get_td_or_sql_qids();
    //---
    $word  = $words_tab[$title] ?? 0;
    $refs  = $ref_tab[$title] ?? 0;
    $asse  = MainTables::$x_Assessments_table[$title] ?? '';
    $views = MainTables::$x_enwiki_pageviews_table[$title] ?? 0;
    $qid   = $sql_qids[$title] ?? "";
    //---
    $target = "";
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

function make_one_row_new($title, $tra_type, $cnt, $langcode, $cat, $camp, $inprocess, $inprocess_table, $tra_btn, $full, $full_tr_user, $mobile_td, $global_username)
{
    //---
    $_user_ = $inprocess_table['user'] ?? '';
    $_date_ = $inprocess_table['date'] ?? $inprocess_table['add_date'] ?? '';
    //---
    $props = one_item_props($title, $langcode, $tra_type);
    //---
    $qid = $props['qid'];
    //---
    // $qid_url = (!empty($qid)) ? "<a class='inline' target='_blank' href='https://wikidata.org/wiki/$qid'>$qid</a>" : '&nbsp;';
    $qid_url = make_wikidata_url_blank($qid);
    //---
    $_user_no_as_global_username = $_user_ != $global_username;
    //---
    // $mdwiki_url = "//mdwiki.org/wiki/" . str_replace('+', '_', rawurlEncode($title));
    $mdwiki_url = make_mdwiki_href($title);
    //---
    $tab = "";
    //---
    $translate_url = $mdwiki_url;
    $full_translate_url = $mdwiki_url;
    //---
    if ($global_username == '') {
        //---
        $tab = <<<HTML
            <a role='button' class='btn btn-outline-primary' onclick='login()'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>
            HTML;
    } else {
        [$tab, $translate_url, $full_translate_url] = make_translate_urls($title, $tra_type, $props['word'], $langcode, $cat, $camp, $inprocess, $tra_btn, $_user_, $full_tr_user, $_user_no_as_global_username);
    }
    //---
    // if $_date_ has : then split before first space
    if (strpos($_date_, ':') !== false) {
        $_date_ = explode(' ', $_date_)[0];
    };
    //---
    $tds = [
        "translate_url" => $translate_url,
        "mdwiki_url" => $mdwiki_url,
        "cnt" => $cnt,
        "title" => $title,
        "tab" => $tab,
        "pageviews" => $props['views'],
        "asse" => $props['asse'],
        "words" => $props['word'],
        "refs" => $props['refs'],
        "qid" => $qid_url,
        "user" => $_user_,
        "date" => $_date_
    ];
    //---
    if ($mobile_td == "mobile") {
        //---
        $mobile_table = make_mobile_table($inprocess, $full_translate_url, $full_tr_user, $tds);
        //---
        return make_td_rows_mobile($full, $inprocess, $mobile_table, $tds);
    };
    //---
    return make_td_rows_responsive($full, $inprocess, $tds);
    // ---
}

function normalizeItemsOld(array $items): array
{
    // Check if the array is associative (keys are not 0..n-1)
    if (array_keys($items) !== range(0, count($items) - 1)) {
        return array_keys($items); // Return only the keys
    }
    // If it's an indexed array, return it as is
    return $items;
}
function normalizeItems(array $items): array
{
    if (array_keys($items) === range(0, count($items) - 1)) {
        return $items;
    }

    $normalized = [];
    foreach ($items as $key => $value) {
        if (is_array($value)) {
            $normalized[] = $key;
        }
    }

    return $normalized;
}

function make_results_table($items, $langcode, $cat, $camp, $tra_type, $tra_btn, $full_tr_user, $global_username, $nolead_translates, $translates_full, $inprocess = false)
{
    //---
    $do_full   = ($tra_type == 'all') ? false : true;
    //---
    // $Translate_th = "<th>Translate</th>";
    //---
    $inprocess_table = ($inprocess) ? $items : [];
    $inprocess_first = '';
    //---
    if ($inprocess) {
        $inprocess_first = '<th>user</th><th>date</th>';
        // ---
        $items = normalizeItems($items);
        // ---
        // if ($tra_btn != '1') $Translate_th = '<th></th>';
    };
    //---
    $table_classes = "sortable table-mobile-responsive";
    //---
    $mobile_td = $_GET["mobile_td"] ?? "1";
    //---
    if ($mobile_td != 'mobile') {
        $table_classes = "display table_responsive";
    }
    //---
    $frist = <<<HTML
        <table class="table compact table-striped table_100 table_text_left $table_classes">
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
                    <th class="spannowrap" style="text-align: center">
                        <span data-bs-toggle="tooltip" data-bs-title="Wikidata identifier">Qid</span>
                    </th>
                    $inprocess_first
                </tr>
            </thead>
            <tbody>
    HTML;
    //---
    $dd = [];
    $dd = sort_py_PageViews($items, MainTables::$x_enwiki_pageviews_table);
    // $dd = sort_py_importance($items, MainTables::$x_Assessments_table);
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
        $cnt2 = $cnt;
        //---
        $inprocess_tab = $inprocess_table[$v] ?? [];
        //---
        if ($inprocess) {
            $tra_type = $inprocess_table['translate_type'] ?? '';
        };
        //---
        if (strtolower(substr($title, 0, 6)) == 'video:') {
            $tra_type = 'all';
        };
        //---
        $row = make_one_row_new($title, $tra_type, $cnt2, $langcode, $cat, $camp, $inprocess, $inprocess_tab, $tra_btn, false, $full_tr_user, $mobile_td, $global_username);
        //---
        // if in process or full translates not allowed
        if ($inprocess || !$do_full || $full_tr_user) {
            $list .= $row;
            $cnt++;
            continue;
        }
        //---
        // if title in no_lead_translates array then $no_lead = true
        $no_lead = (in_array($title, $nolead_translates)) ? true : false;
        //---
        // if title in full_translates array then $full = true
        $full = (in_array($title, $translates_full)) ? true : false;
        //---
        if ($no_lead && !$full) {
            continue;
        }
        //---
        if (!$no_lead) {
            $list .= $row;
        }
        //---
        if ($full) {
            $list .= make_one_row_new($title, 'all', $cnt2, $langcode, $cat, $camp, $inprocess, $inprocess_tab, $tra_btn, true, $full_tr_user, $mobile_td, $global_username);
        }
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
