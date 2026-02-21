<?PHP

namespace Results\Helps;

/*
Usage:

use function Results\Helps\make_translate_urls;
use function Results\Helps\sort_py_PageViews;
use function Results\Helps\sort_py_importance;
use function Results\Helps\normalizeItems;

*/

use Tables\Main\MainTables;

use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function Results\TrLink\make_tr_link_medwiki;
use function TD\Render\Html\make_mdwiki_href;
use function Results\TrLink\make_ContentTranslation_url;

function sort_py_PageViews($items, $en_views_tab)
{
    $dd = [];
    foreach ($items as $t) {
        $t = str_replace('_', ' ', $t);
        $kry = $en_views_tab[$t] ?? 0;
        $dd[$t] = $kry;
    }
    arsort($dd);
    return $dd;
}

function sort_py_importance($items, $Assessment_table)
{
    // ---
    $Assessment_fff = [
        'Top' => 1,
        'High' => 2,
        'Mid' => 3,
        'Low' => 4,
        'Unknown' => 5,
        '' => 5
    ];
    // ---
    $empty = $Assessment_fff['Unknown'];
    $dd = [];
    foreach ($items as $t) {
        $t = str_replace('_', ' ', $t);
        $aa = $Assessment_table[$t] ?? null;
        $kry = $empty;
        if (isset($aa)) {
            $kry = $Assessment_fff[$aa] ?? $empty;
        }
        $dd[$t] = $kry;
    }
    arsort($dd);
    return $dd;
}

function make_translate_urls($title, $tra_type, $words, $langcode, $cat, $camp, $inprocess, $tra_btn, $_user_, $full_tr_user, $_user_no_as_global_username)
{
    //---
    // if $inprocess and $tra_btn is 1 then show the translate button for
    //---
    // $mdwiki_url = "//mdwiki.org/wiki/" . str_replace('+', '_', rawurlEncode($title));
    $mdwiki_url = make_mdwiki_href($title);
    //---
    // if lower $title startswith video
    // $tra_type = "lead";
    if ($tra_type === null || $tra_type === '') {
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
    if ($inprocess) {
        // links directly to ContentTranslation
        $full_translate_url = make_ContentTranslation_url($title, $langcode, $cat, $camp, 'all');
        $translate_url = make_ContentTranslation_url($title, $langcode, $cat, $camp, $tra_type);
    } else {
        // links to translate_med/index.php
        $full_translate_url = make_tr_link_medwiki($title, $langcode, $cat, $camp, "all", $words);
        $translate_url = make_tr_link_medwiki($title, $langcode, $cat, $camp, $tra_type, $words);
    }
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
    if ($inprocess) {
        if ($tra_btn != 1 && $_user_no_as_global_username) {
            $tab = '';
            $translate_url = $mdwiki_url;
            $full_translate_url = $mdwiki_url;
        };
    };
    // ---
    return [$tab, $translate_url, $full_translate_url];
}

function one_item_props($title, $langcode, $tra_type)
{

    $words_tab = ($tra_type == 'all') ? MainTables::$x_All_Words_table : MainTables::$x_Words_table;
    $ref_tab   = ($tra_type == 'all') ? MainTables::$x_All_Refs_table  : MainTables::$x_Lead_Refs_table;
    //---
    $sql_qids = get_td_or_sql_qids();
    //---
    $word  = $words_tab[$title] ?? 0;
    $refs  = $ref_tab[$title] ?? 0;
    $asse  = MainTables::$x_Assessments_table[$title] ?? '';
    $views = MainTables::$x_enwiki_pageviews_table[$title] ?? 0;
    $qid   = $sql_qids[$title] ?? "";
    //---
    $target = "";
    //---
    if (empty($asse)) $asse = 'Unknown';
    //---
    $tab = [
        'word'  => $word,
        'refs'  => $refs,
        'asse'  => $asse,
        'views' => $views,
        'qid'   => $qid,
        'target' => $target
    ];
    //---
    return $tab;
}

function normalizeItems(array $items): array
{
    // If it's an indexed array (0..n-1), return it as-is
    if (array_keys($items) === range(0, count($items) - 1)) {
        return $items;
    }
    // Otherwise, build a list that includes:
    //  - each integer-keyed item’s value
    //  - each associative key whose value is itself an array
    $normalized = [];
    foreach ($items as $key => $value) {
        if (is_int($key)) {
            $normalized[] = $value;
            continue;
        }
        if (is_array($value)) {
            $normalized[] = $key;
        }
    }
    return $normalized;
}
