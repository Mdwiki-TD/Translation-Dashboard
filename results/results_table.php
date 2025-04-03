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

use function Results\TrLink\make_tr_link_medwiki;
use function Tables\SqlTables\load_translate_type;
use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;


$use_medwiki = $settings['use_medwiki']['value'] ?? false;
$full_translators = array_column(get_td_or_sql_full_translators(), 'user');

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

function sort_py_importance($items, MainTables::$x_Assessments_table, MainTables::$x_Assessments_fff)
{
    $empty = MainTables::$x_Assessments_fff['Unknown'] ?? '';
    $dd = [];
    foreach ($items as $t) {
        $t = str_replace('_', ' ', $t);
        $aa = MainTables::$x_Assessments_table[$t] ?? null;
        if (isset($aa)) {
            $kry = MainTables::$x_Assessments_fff[$aa] ?? $empty;
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

    global MainTables::$x_Words_table, MainTables::$x_All_Words_table, MainTables::$x_Assessments_table;
    global MainTables::$x_Lead_Refs_table, MainTables::$x_All_Refs_table, MainTables::$x_enwiki_pageviews_table;
    //---
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
function make_one_row($title, $tra_type, $cnt, $cod, $cat, $camp, $inprocess, $inprocess_table, $tra_btn, $full, $full_tr_user)
{
    global $use_medwiki;
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
    // if ($use_medwiki) {
    //---
    $full_translate_url = make_tr_link_medwiki($title, $cod, $cat, $camp, "all");
    $translate_url = make_tr_link_medwiki($title, $cod, $cat, $camp, $tra_type);
    //---
    // } else {
    // $full_translate_url = make_translate_link($title, $cod, $cat, $camp, "all");
    // $translate_url = make_translate_link($title, $cod, $cat, $camp, $tra_type);
    // }
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
    global MainTables::$x_enwiki_pageviews_table, $full_translators;
    // global TablesSql::$s_no_lead_translates, TablesSql::$s_full_translates;
    //---
    TablesSql::$s_no_lead_translates = load_translate_type('no');
    TablesSql::$s_full_translates = load_translate_type('full');
    //---
    $full_tr_user = in_array($GLOBALS['global_username'], $full_translators);
    //---
    $do_full   = ($tra_type == 'all') ? false : true;
    //---
    $Translate_th = "<th>Translate</th>";
    //---
    $inprocess_table = ($inprocess) ? $items : [];
    $inprocess_first = '';
    //---
    if ($inprocess) {
        $inprocess_first = '<th>user</th><th>date</th>';
        $items = array_keys($items);
        // if ($tra_btn != '1') $Translate_th = '<th></th>';
    };
    //---
    $frist = <<<HTML
    <!-- <div class="table-responsive"> -->
    <table class="table compact sortable table-striped table-mobile-responsive" id="main_table">
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
        $row = make_one_row($title, $tra_type, $cnt2, $cod, $cat, $camp, $inprocess, $inprocess_v, $tra_btn, false, $full_tr_user);
        //---
        // if in process or full translates not allowed
        if ($inprocess || !$do_full || $full_tr_user) {
            $list .= $row;
            $cnt++;
            continue;
        }
        //---
        // if title in no_lead_translates array then $no_lead = true
        $no_lead = (in_array($title, TablesSql::$s_no_lead_translates)) ? true : false;
        //---
        // if title in full_translates array then $full = true
        $full = (in_array($title, TablesSql::$s_full_translates)) ? true : false;
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
            $list .= make_one_row($title, 'all', $cnt2, $cod, $cat, $camp, $inprocess, $inprocess_v, $tra_btn, true, $full_tr_user);
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
