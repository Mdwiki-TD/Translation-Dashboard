<?php

namespace Results\GetResults2026\Rows;

use function Results\TrLink\make_ContentTranslation_url;
use function TD\Render\Html\make_mdwiki_article_url_blank;
use function TD\Render\Html\make_wikipedia_url_blank;
use function TD\Render\Html\make_wikidata_url_blank;

function make_one_row_exists_2026(
    $title,
    $counter,
    $langCode,
    $cat,
    $camp,
    $titleData,
    $globalUsername,
    $userCoord,
    $endpoint
) {

    // target_tab = { "title": "11p deletion syndrome", "category": "RTT", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 838, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153", "target": "متلازمة واجر" , "via":td" }

    $importance  = $titleData['importance'] ?? "Unknown";

    $words = $titleData['w_lead_words'] ?? 0;
    $refs  = $titleData['r_lead_refs'] ?? 0;
    $pageviews = $titleData['en_views'] ?? 0;
    $qid = $titleData['qid'] ?? "";

    $mdwiki_a_tag = make_mdwiki_article_url_blank($title);

    $qidUrl = make_wikidata_url_blank($qid);

    $targetTab = "";
    $targetTab2 = "";

    if ($titleData['target']) {
        if ($titleData["via"] === "td") {
            $targetTab = make_wikipedia_url_blank($titleData['target'], $langCode);
        } else {
            $targetTab2 = make_wikipedia_url_blank($titleData['target'], $langCode);
        }
    }

    $translateUrl = make_ContentTranslation_url(
        $title,
        $langCode,
        $cat,
        $camp,
        'lead',
        $endpoint
    );

    $tab = (!empty($globalUsername) && $userCoord) ? <<<HTML
        <div class='inline'>
            <a href='$translateUrl' class='btn btn-outline-primary btn-sm' target='_blank'>Translate</a>
        </div>
    HTML : "";

    $td22 = <<<HTML
            <td class='num'>
            $pageviews
        </td>
        <td class='num'>
            $importance
        </td>
        <td class='num'>
            $words
        </td>
        <td class='num'>
            $refs
        </td>
    HTML;

    $td22 = "";

    $tdRows = <<<HTML
        <th scope="row" style="text-align:center">
            $counter
        </th>
        <td class='link_container spannowrap'>
            $mdwiki_a_tag
        </td>
        <td>
            $tab
        </td>
        <td>
            $targetTab
        </td>
        <td>
            $targetTab2
        </td>
        $td22
        <td>
            $qidUrl
        </td>
    HTML;

    $tdRows = "<tr>$tdRows</tr>";

    return $tdRows;
}
