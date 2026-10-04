<?php

namespace App\Utils;

class Helps
{
    public static function sort_py_pageviews_rows(array $items, array $en_views_tab): array
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

    public static function sort_py_importance(array $items, array $Assessment_table): array
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
        string $title,
        string $tra_type,
        int|string $words,
        string $langcode,
        string $cat,
        string $camp,
        bool|int|string $inprocess,
        bool|int|string $in_progress_translation_button,
        string $_user_,
        bool $full_tr_user,
        bool $login_user_is_the_translator,
    ): array {

        // if $inprocess and $tra_btn is 1 then show the translate button for

        // $mdwiki_url = "//mdwiki.org/wiki/" . str_replace('+', '_', rawurlencode($title));
        $mdwiki_url = HtmlUrls::make_mdwiki_href($title);

        // if lower $title startswith video
        // $tra_type = "lead";
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

    public static function get_item_properties(string $title, string $tra_type, array $title_data): array
    {

        // inprocess_table = { "title": "Andes virus infection", "user": "Mr. Ibrahem", "lang": "ar", "cat": "RTT", "translate_type": "all", "word": 0, "add_date": "2026-05-21 00:00:00", "campaign": "Main", "autonym": "العربية" }
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
}
