<?php

namespace Results\GetResults2026\Tables;

use function Results\ResultsTableHtml\make_table_start;
use function Results\GetResults2026\_make_one_row_results;

/**
 * Renders the table of missing pages.
 */
function make_results_table_2026(
    $items,
    $langCode,
    $cat,
    $camp,
    $traType,
    $fullTrUser,
    $globalUsername,
    $noLeadTranslates,
    $fullTranslates
) {

    $doFull   = ($traType == 'all') ? false : true;

    $frist = make_table_start(false, false);

    usort($items, function ($a, $b) {
        $viewsA = $a['en_views'] ?? 0;
        $viewsB = $b['en_views'] ?? 0;

        return $viewsB <=> $viewsA;
    });

    // { "title": "11p deletion syndrome", "category": "RTT", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 838, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153", "target": "متلازمة واجر" }
    $items = array_column($items, null, "title");

    $html = "";
    $counter = 1;

    foreach ($items as $title => $titleData) {

        if (empty($title)) {
                continue;
            }

            $title = str_replace("_", " ", $title);


        if (strtolower(substr($title, 0, 6)) == 'video:') {
            $traType = 'all';
        };

        $row = _make_one_row_results(
            $title,
            $traType,
            $counter,
            $langCode,
            $cat,
            $camp,
            false,
            $fullTrUser,
            $globalUsername,
            $titleData
        );

        // if full translates not allowed
        if (!$doFull || $fullTrUser) {
            $html .= $row;
            $counter++;
            continue;
        }

        // if title in no_lead_translates array then $noLead = true
        $noLead = (in_array($title, $noLeadTranslates)) ? true : false;

        // if title in full_translates array then $isFull = true
        $isFull = (in_array($title, $fullTranslates)) ? true : false;

        if ($noLead && !$isFull) {
            continue;
        }

        if (!$noLead) {
            $html .= $row;
        }

        if ($isFull) {
            $html .= _make_one_row_results(
                $title,
                "all",
                $counter,
                $langCode,
                $cat,
                $camp,
                true,
                $fullTrUser,
                $globalUsername,
                $titleData
            );
        }

        $counter++;
    };

    $last = <<<HTML
        </tbody>
    </table>
    HTML;

    return $frist . $html . $last;
}
