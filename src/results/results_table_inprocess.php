<?PHP

namespace Results\ResultsTableInprocess;

/*
Usage:

use function Results\ResultsTableInprocess\make_results_table_inprocess;

*/

use Tables\Main\MainTables;

use function Results\ResultsTable\Rows\make_td_rows_mobile;
use function Results\ResultsTable\Rows\make_mobile_table;
use function TD\Render\Html\make_mdwiki_href;
use function TD\Render\Html\make_wikidata_url_blank;
use function Results\ResultsTableHtml\make_table_start;

use function Results\Helps\make_translate_urls;
use function Results\Helps\sort_py_PageViews;
use function Results\Helps\one_item_props;
use function Results\Helps\normalizeItems;

function make_tds_rows_responsive($full, $tds)
{
    //---
    $tra_btn = $tds["tra_btn"];
    //---
    $mdwiki_url = $tds["mdwiki_url"];
    $cnt    = $tds["cnt"];
    $tab    = $tds["tab"];
    $pviews = $tds["pageviews"];
    $asse   = $tds["asse"];
    $words  = $tds["words"];
    $refs   = $tds["refs"];
    $qid    = $tds["qid"];
    $title  = $tds["title"];
    $_user_ = $tds["user"];
    $_date_ = $tds["date"];
    //---
    $cnt2 = $full ? "$cnt.Full" : $cnt;
    //---
    $tab_th = ($tra_btn == "1") ? "<th>$tab</th>" : "";
    //---
    $td_rows = <<<HTML
        <th class='num' scope="row">
            $cnt2
        </th>
        <td class='link_container'>
            <a target='_blank' href='$mdwiki_url'>$title</a>
        </td>
        <th class=''>
            $tab
        </th>
        <td class='num' style="text-align: left">
            $pviews
        </td>
        <td class='num' style="text-align: left">
            $asse
        </td>
        <td class='num' style="text-align: left">
            $words
        </td>
        <td class='num' style="text-align: left">
            $refs
        </td>
        <td>
            $qid
        </td>
        <td>
            $_user_
        </td>
        <td>
            $_date_
        </td>
    HTML;
    //---
    $td_rows = "<tr class=''>$td_rows</tr>";
    //---
    return $td_rows;
}

function make_one_row_new($title, $tra_type, $cnt, $langcode, $cat, $camp, $inprocess_table, $tra_btn, $full, $full_tr_user, $mobile_td, $global_username)
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
    if ($tra_btn != '1') {
        $translate_url = "";
        $full_translate_url = "";
    } elseif ($global_username == '') {
        //---
        $tab = <<<HTML
            <a role='button' class='btn btn-outline-primary' onclick='login()'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>
            HTML;
    } else {
        [$tab, $translate_url, $full_translate_url] = make_translate_urls($title, $tra_type, $props['word'], $langcode, $cat, $camp, true, $tra_btn, $_user_, $full_tr_user, $_user_no_as_global_username);
    }
    //---
    // if $_date_ has : then split before first space
    if (strpos($_date_, ':') !== false) {
        $_date_ = explode(' ', $_date_)[0];
    };
    //---
    $tds = [
        "tra_btn" => $$tra_btn,
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
        $mobile_table = make_mobile_table(true, $full_translate_url, $full_tr_user, $tds);
        //---
        return make_td_rows_mobile($full, true, $mobile_table, $tds);
    };
    //---
    return make_tds_rows_responsive($full, $tds);
}

function make_results_table_inprocess($inprocess_table, $langcode, $cat, $camp, $tra_btn, $full_tr_user, $global_username, $mobile_td)
{
    //---
    // $inprocess_table = normalizeItems($inprocess_table);
    //---
    $frist = make_table_start($mobile_td, true, $tra_btn);
    //---
    // $dd = sort_py_PageViews($inprocess_table, MainTables::$x_enwiki_pageviews_table);
    //---
    $list = "";
    $cnt = 1;
    //---
    foreach ($inprocess_table as $title => $inprocess_tab) {
        // ---
        if (empty($title)) continue;
        // ---
        // $inprocess_tab = $inprocess_table[$title] ?? [];
        //---
        $title = str_replace('_', ' ', $title);
        //---
        $tra_type = $inprocess_table['translate_type'] ?? '';
        //---
        $full = false;
        //---
        if (strtolower(substr($title, 0, 6)) == 'video:') {
            $tra_type = 'all';
            $full = true;
        };
        //---
        $row = make_one_row_new($title, $tra_type, $cnt, $langcode, $cat, $camp, $inprocess_tab, $tra_btn, $full, $full_tr_user, $mobile_td, $global_username);
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
