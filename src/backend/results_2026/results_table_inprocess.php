<?PHP

namespace Results\GetResults2026;

/*
Usage:

use function Results\GetResults2026\make_results_table_inprocess;

*/

use function TD\Render\Html\make_mdwiki_href;
use function TD\Render\Html\make_wikidata_url_blank;
use function Results\ResultsTableHtml\make_table_start;

use function Results\Helps\make_translate_urls;

function make_tds_rows_responsive($full, $tds)
{
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
    $cnt2 = $full && (strtolower(substr($title, 0, 6)) != 'video:') ? "$cnt.Full" : $cnt;
    //---
    $td_rows = <<<HTML
        <tr class=''>
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
        </tr>
    HTML;
    return $td_rows;
}

function make_one_row_new_inprocess(
    $title,
    $tra_type,
    $cnt,
    $langcode,
    $cat,
    $camp,
    $inprocess_table,
    $tra_btn,
    $full,
    $full_tr_user,
    $global_username,
    $title_data,
    $endpoint
) {
    //---
    // inprocess_table = { "title": "Andes virus infection", "user": "Mr. Ibrahem", "lang": "ar", "cat": "RTT", "translate_type": "all", "word": 0, "add_date": "2026-05-21 00:00:00", "campaign": "Main", "autonym": "العربية" }
    $_user_ = $inprocess_table['user'] ?? '';
    $_date_ = $inprocess_table['date'] ?? $inprocess_table['add_date'] ?? '';
    //---
    $word     = $title_data['w_lead_words'] ?? 0;
    $refs     = $title_data['r_lead_refs'] ?? 0;
    $importance = $title_data['importance'] ?? "";
    $en_views = $title_data['en_views'] ?? "";
    $qid      = $title_data['qid'] ?? "";
    //---
    if ($tra_type == 'all') {
        $word  = $title_data['w_all_words'] ?? 0;
        $refs  = $title_data['r_all_refs'] ?? 0;
    }
    //---
    if (empty($importance)) $importance = 'Unknown';
    //---
    $qid_url = make_wikidata_url_blank($qid);
    //---
    $_user_no_as_global_username = $_user_ != $global_username;
    //---
    $mdwiki_url = make_mdwiki_href($title);
    //---
    $tab = "";
    //---
    $translate_url = $mdwiki_url;
    //---
    if ($tra_btn != '1') {
        $translate_url = "";
    } elseif (!empty($global_username)) {
        [$tab, $translate_url, $_] = make_translate_urls(
            $title,
            $tra_type,
            $word,
            $langcode,
            $cat,
            $camp,
            true,
            $tra_btn,
            $_user_,
            $full_tr_user,
            $_user_no_as_global_username,
            $endpoint
        );
    }
    //---
    // if $_date_ has : then split before first space
    if (strpos($_date_, ':') !== false) {
        $_date_ = explode(' ', $_date_)[0];
    };
    //---
    $tds = [
        "tra_btn" => $tra_btn,
        "translate_url" => $translate_url,
        "mdwiki_url" => $mdwiki_url,
        "cnt" => $cnt,
        "title" => $title,
        "tab" => $tab,
        "pageviews" => $en_views,
        "asse" => $importance,
        "words" => $word,
        "refs" => $refs,
        "qid" => $qid_url,
        "user" => $_user_,
        "date" => $_date_
    ];
    return make_tds_rows_responsive($full, $tds);
}

function make_results_table_inprocess(
    $inprocess_table,
    $langcode,
    $cat,
    $camp,
    $tra_btn,
    $full_tr_user,
    $global_username,
    $titles_infos_items,
    $endpoint
) {
    //---
    // $inprocess_table = normalizeItems($inprocess_table);
    //---
    $frist = make_table_start(true, $tra_btn);
    //---
    $list = "";
    $cnt = 1;
    //---
    foreach ($inprocess_table as $title => $title_tab) {
        // ---
        if (empty($title)) continue;
        // ---
        $title = str_replace('_', ' ', $title);
        //---
        $title_data = $titles_infos_items[$title] ?? [];
        //---
        // { "title": "Andes virus infection", "user": "Mr. Ibrahem", "lang": "ar", "cat": "RTT", "translate_type": "all", "word": 0, "add_date": "2026-05-21 00:00:00", "campaign": "Main", "autonym": "العربية" }
        $tra_type = $title_tab['translate_type'] ?? '';
        //---
        $full = false;
        //---
        if (strtolower(substr($title, 0, 6)) == 'video:') {
            $tra_type = 'all';
            $full = true;
        };
        //---
        $row = make_one_row_new_inprocess(
            $title,
            $tra_type,
            $cnt,
            $langcode,
            $cat,
            $camp,
            $title_tab,
            $tra_btn,
            $full,
            $full_tr_user,
            $global_username,
            $title_data,
            $endpoint
        );
        //--
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
