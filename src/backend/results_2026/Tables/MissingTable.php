<?php

namespace Results\GetResults2026\Tables;

use function Results\ResultsTableHtml\make_table_start;
use function Results\GetResults2026\_make_one_row_results;

function make_results_table_2026(
    $items,
    $langcode,
    $cat,
    $camp,
    $traType,
    $fullTrUser,
    $globalUsername,
    $noLeadTranslates,
    $fullTranslates
) {
    //---
    $doFull   = ($traType == 'all') ? false : true;
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
    foreach ($items as $title => $titleData) {

        if (empty($title)) continue;

        $title = str_replace('_', ' ', $title);
        //---
        $cnt2 = $cnt;
        //---
        if (strtolower(substr($title, 0, 6)) == 'video:') {
            $traType = 'all';
        };
        //---
        $row = _make_one_row_results(
            $title,
            $traType,
            $cnt2,
            $langcode,
            $cat,
            $camp,
            false,
            $fullTrUser,
            $globalUsername,
            $titleData
        );
        //---
        // if full translates not allowed
        if (!$doFull || $fullTrUser) {
            $list .= $row;
            $cnt++;
            continue;
        }
        //---
        // if title in no_lead_translates array then $noLead = true
        $noLead = (in_array($title, $noLeadTranslates)) ? true : false;
        //---
        // if title in full_translates array then $full = true
        $full = (in_array($title, $fullTranslates)) ? true : false;
        //---
        if ($noLead && !$full) {
            continue;
        }
        //---
        if (!$noLead) {
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
                $fullTrUser,
                $globalUsername,
                $titleData
            );
        }
        //---
        $cnt++;
    };

    $last = <<<HTML
        </tbody>
    </table>
    HTML;

    return $frist . $list . $last;
}
