<?PHP

namespace Results\GetResults2026;


use function TD\Render\Html\make_mdwiki_href;
use function TD\Render\Html\make_wikidata_url_blank;
use function Results\TrLink\make_tr_link_medwiki;

function _make_one_row_results(
    $title,
    $traType,
    $counter,
    $langCode,
    $cat,
    $camp,
    $isFull,
    $fullTrUser,
    $globalUsername,
    $titleData
) {

    if (empty($traType)) {
        $traType = 'lead';
    }

    $isVideo = false;

    if (strtolower(substr($title, 0, 6)) == 'video:') {
        $isVideo = true;
        $traType = 'all';
    };

    $words     = $titleData['w_lead_words'] ?? 0;
    $refs     = $titleData['r_lead_refs'] ?? 0;
    $asse     = $titleData['importance'] ?? "";
    $enViews = $titleData['en_views'] ?? "";
    $qid      = $titleData['qid'] ?? "";

    if ($traType == 'all') {
        $words  = $titleData['w_all_words'] ?? 0;
        $refs  = $titleData['r_all_refs'] ?? 0;
    }

    if (empty($asse)) $asse = 'Unknown';

    $qidUrl = make_wikidata_url_blank($qid);

    $mdwikiUrl = make_mdwiki_href($title);

    $tab = "";

    if (empty($globalUsername)) {

        $tab = <<<HTML
            <a role='button' class='btn btn-outline-primary' href='/auth/login.php'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>
            HTML;
    } else {

        $fullTranslateUrl = make_tr_link_medwiki($title, $langCode, $cat, $camp, "all", $words);
        $translateUrl = make_tr_link_medwiki($title, $langCode, $cat, $camp, $traType, $words);

        $tab = "<a href='$translateUrl' class='btn btn-outline-primary btn-sm' target='_blank'>Translate</a>";

        if ($fullTrUser && !$isVideo) {
            $tab = <<<HTML
            <div class='inline'>
                <a href='$translateUrl' class='btn btn-outline-primary btn-sm' target='_blank'>Lead</a>
                <a href='$fullTranslateUrl' class='btn btn-outline-primary btn-sm' target='_blank'>Full</a>
            </div>
        HTML;
        }

    }

    $cnt2 = $isFull && (strtolower(substr($title, 0, 6)) != 'video:') ? "$counter.Full" : $counter;

    $tdRows = <<<HTML
        <th class='num' scope="row">
            $cnt2
        </th>
        <td class='link_container'>
            <a target='_blank' href='$mdwikiUrl'>$title</a>
        </td>
        <th>
            $tab
        </th>
        <td class='num' style="text-align: left">
            $enViews
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
            $qidUrl
        </td>
    HTML;

    $tdRows = "<tr>$tdRows</tr>";

    return $tdRows;
}
