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
    $full_tr_user,
    $global_username,
    $noLeadTranslates,
    $fullTranslates
) {
    //---
    $do_full   = ($traType == 'all') ? false : true;
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
        $no_lead = (in_array($title, $noLeadTranslates)) ? true : false;
        //---
        // if title in full_translates array then $full = true
        $full = (in_array($title, $fullTranslates)) ? true : false;
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
