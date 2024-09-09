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

$use_medwiki = $settings['use_medwiki']['value'] ?? false;

use function Results\TrLink\make_translate_link;
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

function make_one_row($v, $cnt, $cod, $cat, $camp, $words, $refs, $asse, $tra_type, $pageviews, $qid, $inprocess, $in_process, $tra_btn, $full)
{
    global $use_medwiki;
    //---
    $title = str_replace('_', ' ', $v);
    $title2 = rawurlEncode($title);
    $mdwiki_url = "//mdwiki.org/wiki/" . str_replace('+', '_', $title2);
    $qid = (!empty($qid)) ? "<a class='inline' target='_blank' href='https://wikidata.org/wiki/$qid'>$qid</a>" : '&nbsp;';
    //---
    if (empty($asse)) $asse = 'Unknown';
    // "username" => global_username,
    //---
    if ($use_medwiki) {
        $translate_url = make_translate_link_medwiki($title, $cod, $cat, $camp, $tra_type);
    } else {
        $translate_url = make_translate_link($title, $cod, $cat, $camp, $tra_type);
    }
    //---
    $tab = "<a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Translate</a>";
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
    $inprocess_tds = '';
    $_user_ = $in_process[$v]['user'] ?? '';
    $_date_ = $in_process[$v]['date'] ?? '';
    //---
    if ($inprocess) {
        $inprocess_tds = <<<HTML
            <td class='hide_on_mobile_cell' data-content="user">$_user_</td>
            <td class='hide_on_mobile_cell' data-content="Date">$_date_</td>
        HTML;
        if ($tra_btn != '1') {
            $tab = '';
            $translate_url = $mdwiki_url;
        };
    };
    //---
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
    $nq = <<<HTML
        <div class="d-table table-striped">
            $nq_ths
        </div>
    HTML;
    //---
    $cnt2 = $full ? "Full" : $cnt;
    //---
    $div_id = "t_$cnt";
    //---
    if ($inprocess) $div_id .= '_in';
    if ($full) $div_id .= '_full';
    //---
    $lista = <<<HTML
        <tr class="">
            <th class='num hide_on_mobile_cell' scope="row" data-content="$cnt2" data-sort="$cnt">$cnt2</th>
            <td class='link_container spannowrap' data-content="$cnt2">
                <a target='_blank' href='$mdwiki_url' class="hide_on_mobile">$title</a>
                <a target='_blank' href='$translate_url' class="only_on_mobile"><b>$title</b></a>
                <a class="only_on_mobile" style="float:right" data-bs-toggle="collapse" href="#$div_id" role="button" aria-expanded="false" aria-controls="$div_id">+</a>
            </td>
            <th class=''>
                <span class='hide_on_mobile'>$tab</span>
                <div class='collapse' id="$div_id">
                    <div class='only_on_mobile'>$nq</div>
                </div>
            </th>
            <td class='num hide_on_mobile_cell' data-content="Views">$pageviews</td>
            <td class='num hide_on_mobile_cell' data-content="Importance">$asse</td>
            <td class='num hide_on_mobile_cell' data-content="Words">$words</td>
            <td class='num hide_on_mobile_cell' data-content="Refs.">$refs</td>
            <td class='hide_on_mobile_cell' data-content="Qid">$qid</td>
            $inprocess_tds
        </tr>
    HTML;
    //---
    return $lista;
}

function make_results_table($items, $cod, $cat, $camp, $tra_type, $tra_btn, $inprocess = false)
{
    //---
    global $Words_table, $All_Words_table, $Assessments_table;
    global $Lead_Refs_table, $All_Refs_table, $enwiki_pageviews_table;
    global $no_lead_translates, $full_translates;
    //---
    global $sql_qids;
    //---
    $words_tab = ($tra_type == 'all') ? $All_Words_table : $Words_table;
    $ref_tab   = ($tra_type == 'all') ? $All_Refs_table  : $Lead_Refs_table;
    //---
    $do_full   = ($tra_type == 'all') ? false : true;
    //---
    $Refs_word    = 'Refs.';
    $Words_word   = 'Words';
    $Translate_th = "<th>Translate</th>";
    //---
    $in_process = array();
    $inprocess_first = '';
    if ($inprocess) {
        $inprocess_first = '<th>user</th><th>date</th>';
        $in_process = $items;
        $items = array_keys($items);
        if ($tra_btn != '1') $Translate_th = '<th></th>';
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
                    <span data-bs-toggle="tooltip" data-bs-title="number of word of the article in mdwiki.org">$Words_word</span>
                </th>
                <th class="spannowrap">
                    <span data-bs-toggle="tooltip" data-bs-title="number of reference of the article in mdwiki.org">$Refs_word</span>
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
        $views = $enwiki_pageviews_table[$title] ?? 0;
        $word  = $words_tab[$title] ?? 0;
        $refs  = $ref_tab[$title] ?? 0;
        $asse  = $Assessments_table[$title] ?? '';
        $qid   = $sql_qids[$title] ?? "";
        //---
        $cnt2 = $cnt;
        //---
        $row = make_one_row($v, $cnt2, $cod, $cat, $camp, $word, $refs, $asse, $tra_type, $views, $qid, $inprocess, $in_process, $tra_btn, false);
        //---
        // if in process or full translates not allowed
        if ($inprocess || !$do_full) {
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
            $word  = $All_Words_table[$title] ?? 0;
            $refs  = $All_Refs_table[$title] ?? 0;
            //---
            $list .= make_one_row($v, $cnt2, $cod, $cat, $camp, $word, $refs, $asse, 'all', $views, $qid, $inprocess, $in_process, $tra_btn, true);
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
