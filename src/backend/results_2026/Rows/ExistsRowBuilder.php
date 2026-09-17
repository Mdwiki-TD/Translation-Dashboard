<?php

namespace Results\GetResults2026\Rows;

use function Results\TrLink\make_ContentTranslation_url;
use function TD\Render\Html\make_mdwiki_article_url_blank;
use function TD\Render\Html\make_wikipedia_url_blank;
use function TD\Render\Html\make_wikidata_url_blank;

function make_one_row_exists_2026(
    $title,
    $cnt,
    $langcode,
    $cat,
    $camp,
    $title_data,
    $global_username,
    $user_coord,
    $endpoint
) {
    //---
    // target_tab = { "title": "11p deletion syndrome", "category": "RTT", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 838, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153", "target": "متلازمة واجر" , "via":td" }
    //---
    $importance  = $title_data['importance'] ?? "Unknown";
    //---
    $words = $title_data['w_lead_words'] ?? 0;
    $refs  = $title_data['r_lead_refs'] ?? 0;
    $pageviews = $title_data['en_views'] ?? 0;
    $qid = $title_data['qid'] ?? "";
    //---
    $mdwiki_a_tag = make_mdwiki_article_url_blank($title);
    //---
    $qid_url = make_wikidata_url_blank($qid);
    //---
    $target_tab = "";
    $target_tab2 = "";
    //---
    if ($title_data['target']) {
        if ($title_data["via"] === "td") {
            $target_tab = make_wikipedia_url_blank($title_data['target'], $langcode);
        } else {
            $target_tab2 = make_wikipedia_url_blank($title_data['target'], $langcode);
        }
    }
    //---
    $translate_url = make_ContentTranslation_url(
        $title,
        $langcode,
        $cat,
        $camp,
        'lead',
        $endpoint
    );
    //---
    $tab = (!empty($global_username) && $user_coord) ? <<<HTML
        <div class='inline'>
            <a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Translate</a>
        </div>
    HTML : "";
    //---
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
    //---
    $td22 = "";
    //---
    $td_rows = <<<HTML
        <th class='' scope="row" style="text-align: center">
            $cnt
        </th>
        <td class='link_container spannowrap'>
            $mdwiki_a_tag
        </td>
        <td>
            $tab
        </td>
        <td>
            $target_tab
        </td>
        <td>
            $target_tab2
        </td>
        $td22
        <td>
            $qid_url
        </td>
    HTML;
    //---
    $td_rows = "<tr class=''>$td_rows</tr>";
    //---
    return $td_rows;
}
