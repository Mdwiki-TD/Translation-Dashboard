<?PHP

namespace Results\GetResults2026;


use function TD\Render\Html\make_mdwiki_href;
use function TD\Render\Html\make_wikidata_url_blank;
use function Results\TrLink\make_tr_link_medwiki;

function _make_one_row_results(
    $title,
    $traType,
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
    if (empty($traType)) {
        $traType = 'lead';
    }
    //---
    $is_video = false;
    //---
    if (strtolower(substr($title, 0, 6)) == 'video:') {
        $is_video = true;
        $traType = 'all';
    };
    //---
    $words     = $title_data['w_lead_words'] ?? 0;
    $refs     = $title_data['r_lead_refs'] ?? 0;
    $asse     = $title_data['importance'] ?? "";
    $en_views = $title_data['en_views'] ?? "";
    $qid      = $title_data['qid'] ?? "";
    //---
    if ($traType == 'all') {
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
        $translate_url = make_tr_link_medwiki($title, $langcode, $cat, $camp, $traType, $words);
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
