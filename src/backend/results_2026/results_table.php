<?PHP

namespace Results\GetResults2026;

/*
Usage:

use function Results\GetResults2026\make_results_table;

*/

use function TD\Render\Html\make_mdwiki_href;
use function TD\Render\Html\make_wikidata_url_blank;
use function Results\ResultsTableHtml\make_table_start;
use function Results\TrLink\make_tr_link_medwiki;

function _make_one_row_results(
    $title,
    $tra_type,
    $cnt,
    $langcode,
    $cat,
    $camp,
    $full,
    $full_tr_user,
    $global_username,
    $title_data
) {
    //---
    if (empty($tra_type)) {
        $tra_type = 'lead';
    }
    //---
    $is_video = false;
    //---
    if (strtolower(substr($title, 0, 6)) == 'video:') {
        $is_video = true;
        $tra_type = 'all';
    };
    //---
    $words     = $title_data['w_lead_words'] ?? 0;
    $refs     = $title_data['r_lead_refs'] ?? 0;
    $asse     = $title_data['importance'] ?? "";
    $en_views = $title_data['en_views'] ?? "";
    $qid      = $title_data['qid'] ?? "";
    //---
    if ($tra_type == 'all') {
        $words  = $title_data['w_all_words'] ?? 0;
        $refs  = $title_data['r_all_refs'] ?? 0;
    }
    //---
    if (empty($asse)) $asse = 'Unknown';
    //---
    $qid_url = make_wikidata_url_blank($qid);
    //---
    $mdwiki_url = make_mdwiki_href($title);
    //---
    $tab = "";
    //---
    if (empty($global_username)) {
        //---
        $tab = <<<HTML
            <a role='button' class='btn btn-outline-primary' href='/auth/login.php'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>
            HTML;
    } else {
        //---
        $full_translate_url = make_tr_link_medwiki($title, $langcode, $cat, $camp, "all", $words);
        $translate_url = make_tr_link_medwiki($title, $langcode, $cat, $camp, $tra_type, $words);
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
    }
    //---
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
            $en_views
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
            $qid_url
        </td>
    HTML;
    //---
    $td_rows = "<tr class=''>$td_rows</tr>";
    //---
    return $td_rows;
}

function make_results_table_2026(
    $items,
    $langcode,
    $cat,
    $camp,
    $tra_type,
    $full_tr_user,
    $global_username,
    $nolead_translates,
    $translates_full
) {
    //---
    $do_full   = ($tra_type == 'all') ? false : true;
    //---
    $frist = make_table_start(false, false);
    //---
    usort($items, function ($a, $b) {
        $viewsA = $a['en_views'] ?? 0;
        $viewsB = $b['en_views'] ?? 0;

        return $viewsB <=> $viewsA;
    });
    //---
    // { "title": "11p deletion syndrome", "category": "RTT", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 838, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153", "target": "متلازمة واجر" }
    $items = array_column($items, null, "title");
    //---
    $list = "";
    $cnt = 1;
    //---
    foreach ($items as $title => $title_data) {
        // ---
        if (empty($title)) continue;
        // ---
        $title = str_replace('_', ' ', $title);
        //---
        $cnt2 = $cnt;
        //---
        if (strtolower(substr($title, 0, 6)) == 'video:') {
            $tra_type = 'all';
        };
        //---
        $row = _make_one_row_results(
            $title,
            $tra_type,
            $cnt2,
            $langcode,
            $cat,
            $camp,
            false,
            $full_tr_user,
            $global_username,
            $title_data
        );
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
            $list .= _make_one_row_results(
                $title,
                'all',
                $cnt2,
                $langcode,
                $cat,
                $camp,
                true,
                $full_tr_user,
                $global_username,
                $title_data
            );
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
