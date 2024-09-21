<?PHP

namespace Results\ResultsTable;

/*
Usage:

use function Results\ResultsTable\sort_py_PageViews;
use function Results\ResultsTable\sort_py_importance;
use function Results\ResultsTable\make_one_row;
use function Results\ResultsTable\make_results_table;

*/

include_once __DIR__ . '/../Tables/tables.php';
include_once __DIR__ . '/../Tables/sql_tables.php';

use function Results\TrLink\make_translate_link;
use function Results\TrLink\make_translate_link_medwiki;

$use_medwiki = $settings['use_medwiki']['value'] ?? false;

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

function sort_py_importance($items, $Assessments_table, $Assessments_fff)
{
    $empty = $Assessments_fff['Unknown'] ?? '';
    $dd = [];
    foreach ($items as $t) {
        $t = str_replace('_', ' ', $t);
        $aa = $Assessments_table[$t] ?? null;
        if (isset($aa)) {
            $kry = $Assessments_fff[$aa] ?? $empty;
        }
        $dd[$t] = $kry;
    }
    arsort($dd);
    return $dd;
}

function make_mobile_table($words, $refs, $asse, $pageviews, $qid, $inprocess, $_user_, $_date_, $full_translate_url, $full_tr_user)
{
    // Define an array to store the values
    $data = array(
        array("Views", $pageviews),
        array("Importance", $asse),
        array("Words", $words),
        array("Refs.", $refs),
        array("Qid", $qid)
    );
    if ($inprocess) {
        // add User : $_user_ and Date : $_date_
        $data[] = array("User", $_user_);
        $data[] = array("Date", $_date_);
    };

    // Initialize an empty string to store the generated HTML
    $nq_ths = '';

    // if ($full_tr_user && !$inprocess) {
    if ($full_tr_user && !$inprocess) {
        $nq_ths = <<<HTML
                <div class="d-table-row">
                    <span class="d-table-cell px-2" style="color:#54667a;">Full Translate</span>
                    <span class="d-table-cell px-2" style='font-weight: normal;'><a class='inline' target='_blank' href='$full_translate_url'>Translate</a></span>
                </div>
            HTML;
    }

    // Loop through the array and generate the HTML
    foreach ($data as $item) {
        $nq_ths .= <<<HTML
                <div class="d-table-row">
                    <span class="d-table-cell px-2" style="color:#54667a;">{$item[0]}</span>
                    <span class="d-table-cell px-2" style='font-weight: normal;'>{$item[1]}</span>
                </div>
            HTML;
    }
    //---
    $nxqe = <<<HTML
            <div class="d-table table-striped">
                $nq_ths
            </div>
        HTML;
    //---
    return $nxqe;
}

function one_item_props($title, $tra_type)
{

    global $Words_table, $All_Words_table, $Assessments_table;
    global $Lead_Refs_table, $All_Refs_table, $enwiki_pageviews_table;
    //---
    $words_tab = ($tra_type == 'all') ? $All_Words_table : $Words_table;
    $ref_tab   = ($tra_type == 'all') ? $All_Refs_table  : $Lead_Refs_table;
    //---
    global $sql_qids;
    //---
    $word  = $words_tab[$title] ?? 0;
    $refs  = $ref_tab[$title] ?? 0;
    $asse  = $Assessments_table[$title] ?? '';
    $views = $enwiki_pageviews_table[$title] ?? 0;
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
function make_one_row($title, $tra_type, $cnt, $cod, $cat, $camp, $inprocess, $in_process, $tra_btn, $full, $full_tr_user)
{
    global $use_medwiki;
    //---
    $cnt2 = $full ? "Full" : $cnt;
    //---
    $div_id = "t_$cnt";
    //---
    $_translate_type_ = $in_process['translate_type'] ?? '';
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
    if ($use_medwiki) {
        $full_translate_url = make_translate_link_medwiki($title, $cod, $cat, $camp, "all");
        $translate_url = make_translate_link_medwiki($title, $cod, $cat, $camp, $tra_type);
    } else {
        $full_translate_url = make_translate_link($title, $cod, $cat, $camp, "all");
        $translate_url = make_translate_link($title, $cod, $cat, $camp, $tra_type);
    }
    //---
    $tab = "<a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Translate</a>";
    //---
    if ($full_tr_user) {
        $tab = <<<HTML
            <div class='inline'>
                <a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Lead</a>
                <a href='$full_translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Full</a>
            </div>
            HTML;
    }
    //---
    if (global_username == '') {
        $tab = <<<HTML
            <a role='button' class='btn btn-outline-primary' onclick='login()'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>
            HTML;
        //---
        $translate_url = $mdwiki_url;
    }
    //---
    $_user_ = $in_process['user'] ?? '';
    $_date_ = $in_process['date'] ?? '';
    //---
    if ($inprocess) {
        if ($tra_btn != '1') {
            $tab = '';
            $translate_url = $mdwiki_url;
            $full_translate_url = $mdwiki_url;
        };
    };
    //---
    $mobile_table = make_mobile_table($words, $refs, $asse, $pageviews, $qid, $inprocess, $_user_, $_date_, $full_translate_url, $full_tr_user);
    //---
    $td_rows = <<<HTML
        <th class='num hide_on_mobile_cell' scope="row" data-content="$cnt2" data-sort="$cnt">$cnt2</th>
        <td class='link_container spannowrap' data-content="$cnt2">
            <a target='_blank' href='$mdwiki_url' class='hide_on_mobile'>$title</a>
            <a target='_blank' href='$translate_url' class="only_on_mobile"><b>$title</b></a>
            <a class="only_on_mobile" style="float:right" data-bs-toggle="collapse" href="#$div_id" role="button" aria-expanded="false" aria-controls="$div_id">+</a>
        </td>

        <th class=''>
            <span class='hide_on_mobile'>$tab</span>
            <div class='collapse' id="$div_id">
                <div class='only_on_mobile'>$mobile_table</div>
            </div>
        </th>

        <td class='num hide_on_mobile_cell' data-content="Views">$pageviews</td>
        <td class='num hide_on_mobile_cell' data-content="Importance">$asse</td>
        <td class='num hide_on_mobile_cell' data-content="Words">$words</td>
        <td class='num hide_on_mobile_cell' data-content="Refs.">$refs</td>
        <td class='hide_on_mobile_cell' data-content="Qid">$qid</td>
    HTML;
    //---
    if ($inprocess) {
        $td_rows .= <<<HTML
            <td class='hide_on_mobile_cell' data-content="user">$_user_</td>
            <td class='hide_on_mobile_cell' data-content="Date">$_date_</td>
        HTML;
    };
    //---
    $td_rows = "<tr class=''>$td_rows</tr>";
    //---
    return $td_rows;
}

function make_results_table($items, $cod, $cat, $camp, $tra_type, $tra_btn, $inprocess = false)
{
    //---
    global $enwiki_pageviews_table;
    global $no_lead_translates, $full_translates;
    //---
    global $full_translators;
    //---
    $full_tr_user = in_array(global_username, $full_translators);
    //---
    $do_full   = ($tra_type == 'all') ? false : true;
    //---
    $Translate_th = "<th>Translate</th>";
    //---
    $in_process = ($inprocess) ? $items : array();
    $inprocess_first = '';
    //---
    if ($inprocess) {
        $inprocess_first = '<th>user</th><th>date</th>';
        $items = array_keys($items);
        if ($tra_btn != '1') {
            $Translate_th = '<th></th>';
        }
    };
    //---
    $frist = <<<HTML
    <!-- <div class="table-responsive"> -->
    <table class="table compact sortable table-striped table-mobile-responsive table-mobile-sided" id="main_table">
        <thead>
            <tr>
                <th class="num">
                    #
                </th>
                <th class="spannowrap">
                    Title
                </th>
                $Translate_th
                <th class="spannowrap">
                    <span data-bs-toggle="tooltip" data-bs-title="Page views in last month in English Wikipedia">Views</span>
                </th>
                <th class="spannowrap">
                    <span data-bs-toggle="tooltip" data-bs-title="Page important from medicine project in English Wikipedia">Importance</span>
                </th>
                <th class="spannowrap">
                    <span data-bs-toggle="tooltip" data-bs-title="number of words of the article in mdwiki.org">Words</span>
                </th>
                <th class="spannowrap">
                    <span data-bs-toggle="tooltip" data-bs-title="number of references of the article in mdwiki.org">Refs.</span>
                </th>
                <th class="spannowrap">
                    <span data-bs-toggle="tooltip" data-bs-title="Wikidata identifier">Qid</span>
                </th>
                $inprocess_first
            </tr>
        </thead>
        <tbody>
    HTML;
    //---
    $dd = array();
    $dd = sort_py_PageViews($items, $enwiki_pageviews_table);
    // $dd = sort_py_importance($items, $Assessments_table, $Assessments_fff);
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
        $in_process_v = $in_process[$v] ?? [];
        //---
        $row = make_one_row($title, $tra_type, $cnt2, $cod, $cat, $camp, $inprocess, $in_process_v, $tra_btn, false, $full_tr_user);
        //---
        // if in process or full translates not allowed
        if ($inprocess || !$do_full || $full_tr_user) {
            $list .= $row;
            $cnt++;
            continue;
        }
        //---
        // if title in no_lead_translates array then $no_lead = true
        $no_lead = (in_array($title, $no_lead_translates)) ? true : false;
        //---
        // if title in full_translates array then $full = true
        $full = (in_array($title, $full_translates)) ? true : false;
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
            $list .= make_one_row($title, 'all', $cnt2, $cod, $cat, $camp, $inprocess, $in_process_v, $tra_btn, true, $full_tr_user);
        }
        //---
        $cnt++;
    };
    $last = <<<HTML
        </tbody>
    </table>
    <!-- </div> -->
    HTML;
    return $frist . $list . $last;
}
