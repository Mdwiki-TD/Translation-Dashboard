<?PHP

namespace Results\ResultsTable;

/*
Usage:

use function Results\ResultsTable\make_results_table;

*/

use Tables\Main\MainTables;

use function TD\Render\Html\make_mdwiki_href;
use function TD\Render\Html\make_wikidata_url_blank;
use function Results\ResultsTableHtml\make_table_start;

use function Results\Helps\make_translate_urls;
use function Results\Helps\sort_py_PageViews;
use function Results\Helps\get_item_properties;

function make_td_rows_responsive($full, $inprocess, $tds)
{
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
    // $cnt2 = $full ? "$cnt.Full" : $cnt;
    $cnt2 = $full && (strtolower(substr($title, 0, 6)) != 'video:') ? "$cnt.Full" : $cnt;
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
    HTML;
    //---
    if ($inprocess) {
        $td_rows .= <<<HTML
            <td>
                $_user_
            </td>
            <td>
                $_date_
            </td>
        HTML;
    };
    //---
    $td_rows = "<tr class=''>$td_rows</tr>";
    //---
    return $td_rows;
}

function make_one_row_results($title, $tra_type, $cnt, $langcode, $cat, $camp, $full, $full_tr_user, $global_username)
{
    //---
    $props = get_item_properties($title, $langcode, $tra_type);
    //---
    $qid = $props['qid'];
    //---
    // $qid_url = (!empty($qid)) ? "<a class='inline' target='_blank' href='https://wikidata.org/wiki/$qid'>$qid</a>" : '&nbsp;';
    $qid_url = make_wikidata_url_blank($qid);
    //---
    // $mdwiki_url = "//mdwiki.org/wiki/" . str_replace('+', '_', rawurlEncode($title));
    $mdwiki_url = make_mdwiki_href($title);
    //---
    $tab = "";
    //---
    $translate_url = $mdwiki_url;
    $full_translate_url = $mdwiki_url;
    //---
    if (empty($global_username)) {
        //---
        $tab = <<<HTML
            <a role='button' class='btn btn-outline-primary' href='/auth/login.php'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>
            HTML;
    } else {
        [$tab, $translate_url, $full_translate_url] = make_translate_urls($title, $tra_type, $props['word'], $langcode, $cat, $camp, false, "", "", $full_tr_user, false);
    }
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
        "user" => "",
        "date" => ""
    ];
    return make_td_rows_responsive($full, false, $tds);
}

function make_results_table($items, $langcode, $cat, $camp, $tra_type, $full_tr_user, $global_username, $nolead_translates, $translates_full)
{
    //---
    $do_full   = ($tra_type == 'all') ? false : true;
    //---
    $frist = make_table_start(false, false);
    //---
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
        if (strtolower(substr($title, 0, 6)) == 'video:') {
            $tra_type = 'all';
        };
        //---
        $row = make_one_row_results($title, $tra_type, $cnt2, $langcode, $cat, $camp, false, $full_tr_user, $global_username);
        //---
        // if full translates not allowed
        if (!$do_full || $full_tr_user) {
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
            $list .= make_one_row_results($title, 'all', $cnt2, $langcode, $cat, $camp, true, $full_tr_user, $global_username);
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
