<?php

namespace App\Utils;

class Helps
{
    public static function sort_py_pageviews_rows($items, $en_views_tab)
    {
        $dd = [];
        foreach ($items as $t) {
            $t = str_replace("_", " ", $t);
            $kry = $en_views_tab[$t] ?? 0;
            $dd[$t] = $kry;
        }
        arsort($dd);
        return $dd;
    }

    public static function sort_py_importance($items, $Assessment_table)
    {
        $Assessment_fff = [
            'Top' => 1,
            'High' => 2,
            'Mid' => 3,
            'Low' => 4,
            'Unknown' => 5,
            '' => 5
        ];

        $empty = $Assessment_fff['Unknown'];
        $dd = [];
        foreach ($items as $t) {
            $t = str_replace("_", " ", $t);
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

    public static function make_translate_urls(
        $title,
        $tra_type,
        $words,
        $langcode,
        $cat,
        $camp,
        $inprocess,
        $in_progress_translation_button,
        $_user_,
        $full_tr_user,
        $login_user_is_the_translator,
    ) {
        $mdwiki_url = HtmlUrls::make_mdwiki_href($title);

        if (empty($tra_type)) {
            $tra_type = 'lead';
        }

        $is_video = false;

        if (strtolower(substr($title, 0, 6)) == 'video:') {
            $is_video = true;
            $tra_type = 'all';
        }

        if ($inprocess) {
            // links directly to ContentTranslation
            $full_translate_url = TrLink::makeContentTranslationUrl(
                $title,
                $langcode,
                $cat,
                $camp,
                'all',
            );
            $translate_url = TrLink::makeContentTranslationUrl(
                $title,
                $langcode,
                $cat,
                $camp,
                $tra_type,
            );
        } else {
            // links to translate_med/index.php
            $full_translate_url = TrLink::makeTrLinkMedwiki($title, $langcode, $cat, $camp, "all", $words);
            $translate_url = TrLink::makeTrLinkMedwiki($title, $langcode, $cat, $camp, $tra_type, $words);
        }

        $buttons = "<a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Translate</a>";

        if ($full_tr_user && !$is_video) {
            $buttons = <<<HTML
                <div class='inline'>
                    <a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Lead</a>
                    <a href='$full_translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Full</a>
                </div>
            HTML;
        }

        if ($inprocess) {
            if ($in_progress_translation_button != 1 && !$login_user_is_the_translator) {
                $buttons = '';
                $translate_url = $mdwiki_url;
                $full_translate_url = $mdwiki_url;
            }
        }

        return [$buttons, $translate_url, $full_translate_url];
    }

    public static function get_item_properties($title, $tra_type, $title_data)
    {
        $word     = $title_data['w_lead_words'] ?? 0;
        $refs     = $title_data['r_lead_refs'] ?? 0;
        $asse     = $title_data['importance'] ?? "";
        $en_views = $title_data['en_views'] ?? "";
        $qid      = $title_data['qid'] ?? "";

        if ($tra_type == 'all') {
            $word  = $title_data['w_all_words'] ?? 0;
            $refs  = $title_data['r_all_refs'] ?? 0;
        }

        if (empty($asse)) $asse = 'Unknown';

        $tab = [
            'word'  => $word,
            'refs'  => $refs,
            'asse'  => $asse,
            'views' => $en_views,
            'qid'   => $qid,
            'target' => ""
        ];

        return $tab;
    }

    public static function normalizeItems(array $items): array
    {
        if (array_keys($items) === range(0, count($items) - 1)) {
            return $items;
        }
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
}
