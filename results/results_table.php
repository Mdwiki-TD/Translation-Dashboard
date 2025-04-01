<?PHP

namespace Results\ResultsTable;

/*
Usage:

use function Results\ResultsTable\sort_py_PageViews;
use function Results\ResultsTable\sort_py_importance;
use function Results\ResultsTable\make_one_row;
use function Results\ResultsTable\make_results_table;

*/

include_once __DIR__ . '/../Tables/include.php';

use Tables\Main\MainTables;
use Tables\SqlTables\TablesSql;

use function Results\TrLink\make_tr_link_medwiki;
use function Tables\SqlTables\load_translate_type;
use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;

$use_medwiki = TablesSql::$s_settings['use_medwiki']['value'] ?? false;

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

function sort_py_importance($items, $Assessment_table, $Assessment_fff)
{
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

function one_item_props($title, $tra_type)
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
function make_one_row_new($title, $tra_type, $cnt, $cod, $cat, $camp, $inprocess, $inprocess_table, $tra_btn, $full, $full_tr_user)
{
    //---
    $cnt2 = $full ? "Full" : $cnt;
    //---
    $div_id = "t_$cnt";
    //---
    $_translate_type_ = $inprocess_table['translate_type'] ?? '';
    //---
    $is_video = false;
    //---
    // if lower $title startswith video
    if (strtolower(substr($title, 0, 6)) == 'video:') {
        $is_video = true;
        $tra_type = 'all';
    };
    //---
    if ($inprocess) {
        $tra_type = $_translate_type_;
    };
    //---
    $props = one_item_props($title, $tra_type);
    //---
    $words = $props['word'];
    $refs  = $props['refs'];
    $asse  = $props['asse'];
    $pageviews = $props['views'];
    $qid = $props['qid'];
    //---
    if ($inprocess) $div_id .= '_in';
    if ($full) $div_id .= '_full';
    //---
    $title2 = rawurlEncode($title);
    $mdwiki_url = "//mdwiki.org/wiki/" . str_replace('+', '_', $title2);
    $qid = (!empty($qid)) ? "<a class='inline' target='_blank' href='https://wikidata.org/wiki/$qid'>$qid</a>" : '&nbsp;';
    //---
    $full_translate_url = make_tr_link_medwiki($title, $cod, $cat, $camp, "all", $words);
    $translate_url = make_tr_link_medwiki($title, $cod, $cat, $camp, $tra_type, $words);
    //---
    $tab = "<a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Translate</a>";
    //---
    if ($full_tr_user && !$is_video) {
        $tab = <<<HTML
            <div class='inline'>
                <a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Lead</a>
                <a href='$full_translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Full</a>
            </div>
            HTML;
    }
    //---
    if ($GLOBALS['global_username'] == '') {
        $tab = <<<HTML
            <a role='button' class='btn btn-outline-primary' onclick='login()'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>
            HTML;
        //---
        $translate_url = $mdwiki_url;
    }
    //---
    $_user_ = $inprocess_table['user'] ?? '';
    $_date_ = $inprocess_table['date'] ?? $inprocess_table['add_date'] ?? '';
    //---
    // if $_date_ has : then split before first space
    if (strpos($_date_, ':') !== false) {
        $_date_ = explode(' ', $_date_)[0];
    };
    //---
    if ($inprocess) {
        if ($tra_btn != '1' && $_user_ != $GLOBALS['global_username']) {
            $tab = '';
            $translate_url = $mdwiki_url;
            $full_translate_url = $mdwiki_url;
        };
    };
    //---
    $td_rows = <<<HTML
        <th class='' scope="row" data-sort="$cnt" style="text-align: left">$cnt2</th>
        <th class='link_container spannowrap' style="font-weight: normal;">
            <a target='_blank' href='$mdwiki_url' class='hide_on_mobile'>$title</a>
            <a target='_blank' href='$translate_url' class="only_on_mobile"><b>$title</b></a>
        </th>

        <th>
            <span class=''>$tab</span>
        </th>

        <td class='num' style="text-align: left">$pageviews</td>
        <td class='num' style="text-align: left">$asse</td>
        <td class='num' style="text-align: left">$words</td>
        <td class='num' style="text-align: left">$refs</td>
        <td>$qid</td>
    HTML;
    //---
    if ($inprocess) {
        $td_rows .= <<<HTML
            <td>$_user_</td>
            <td>$_date_</td>
        HTML;
    };
    //---
    $td_rows = "<tr>$td_rows</tr>";
    //---
    return $td_rows;
}
function make_results_table($items, $cod, $cat, $camp, $tra_type, $tra_btn, $inprocess = false)
{
    //---
    $full_translators = array_column(get_td_or_sql_full_translators(), 'user');
    //---
    $nolead_translates = load_translate_type('no');
    $translates_full = load_translate_type('full');
    //---
    $full_tr_user = in_array($GLOBALS['global_username'], $full_translators);
    //---
    $do_full   = ($tra_type == 'all') ? false : true;
    //---
    // $Translate_th = "<th>Translate</th>";
    //---
    $inprocess_table = ($inprocess) ? $items : [];
    $inprocess_first = '';
    //---
    if ($inprocess) {
        $inprocess_first = '<th class="not-mobile">user</th><th class="not-mobile">date</th>';
        $items = array_keys($items);
        // if ($tra_btn != '1') $Translate_th = '<th></th>';
    };
    //---
    $frist = <<<HTML
    <!-- <div class="table-responsive"> -->
    <table class="table display table-striped table_responsive">
        <thead>
            <tr>
                <th class="all">
                    #
                </th>
                <th class="spannowrap all" data-priority="1">
                    Title
                </th>
                <th class="not-mobile">
                    <span class=''>Translate</span>
                </th>
                <th class="spannowrap not-mobile"  style="text-align: center">
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
                $inprocess_first
            </tr>
        </thead>
        <tbody>
    HTML;
    //---
    $dd = [];
    $dd = sort_py_PageViews($items, MainTables::$x_enwiki_pageviews_table);
    // $dd = sort_py_importance($items, MainTables::$x_Assessments_table, MainTables::$x_Assessments_fff);
    //---
    $list = "";
    $cnt = 1;
    //---
    foreach ($dd as $v => $gt) {
        if (empty($v)) continue;
        $title = str_replace('_', ' ', $v);
        //---
        $cnt2 = $cnt;
        //---
        $inprocess_v = $inprocess_table[$v] ?? [];
        //---
        $row = make_one_row_new($title, $tra_type, $cnt2, $cod, $cat, $camp, $inprocess, $inprocess_v, $tra_btn, false, $full_tr_user);
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
            $list .= make_one_row_new($title, 'all', $cnt2, $cod, $cat, $camp, $inprocess, $inprocess_v, $tra_btn, true, $full_tr_user);
        }
        //---
        $cnt++;
    };
    $last = <<<HTML
            </tbody>
        </table>
    HTML;
    return $frist . $list . $last;
}
