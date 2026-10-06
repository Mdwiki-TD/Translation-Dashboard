<?php

namespace App\Leaderboard\Helpers\Langs;

class LeaderTablesLangs
{
    public static function createNumbersTable(
        int|string $c_user,
        int|string $c_articles,
        int|string $c_words,
        int|string $c_langs,
        int|string $c_views
    ): string {
        return <<<HTML
        <table class='table compact table-striped'>
            <thead>
                <tr>
                    <th class="spannowrap">Type</th>
                    <th>Number</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><b>Users</b></td><td>$c_user</td></tr>
                <tr><td><b>Articles</b></td><td>$c_articles</td></tr>
                <tr><td><b>Words</b></td><td>$c_words</td></tr>
                <tr><td><b>Languages</b></td><td>$c_langs</td></tr>
                <tr><td><b>Pageviews</b></td><td>$c_views</td></tr>
            </tbody>
        </table>
        HTML;
    }

    public static function makeLangTable(
        array $lang_table,
        array $langs_data,
        bool $addcat
    ): string {
        uasort($lang_table, function ($a, $b): int {
            return $b["count"] <=> $a["count"];
        });

        $numb = 0;
        $text = "";
        $cac = ($addcat == true) ? '<th>cat</th>' : '';

        foreach ($lang_table as $langcode => $tab) {
            $comp = $tab['count'];
            $views = $tab['views'];
            $langname = $tab['lang_name'] ?? $langs_data[$langcode]['name'] ?? $langcode;

            if ($comp < 1) continue;
            $comp = number_format($comp);
            $numb++;

            $view = number_format($views);

            $cach = <<<HTML
                <td><a target="_blank" href="https://$langcode.wikipedia.org/wiki/Category:Translated_from_MDWiki">cat</a></td>
            HTML;
            if ($addcat != true) $cach = '';

            $text .= <<<HTML
                <tr>
                    <td>$numb</td>
                    <td><a href='leaderboard.php?get=langs&langcode=$langcode'>$langname</a></td>
                    <td>$comp</td>
                    <td>$view</td>
                    $cach
                </tr>
            HTML;
        }

        return <<<HTML
            <table class='table compact table-striped sortable table_text_left leaderboard_tables' style='margin-top: 0px !important;margin-bottom: 0px !important'>
                <thead>
                    <tr>
                        <th>#</th>
                        <th class='spannowrap'>Language</th>
                        <th>Count</th>
                        <th>Pageviews</th>
                        $cac
                    </tr>
                </thead>
                <tbody>
                    {$text}
                </tbody>
            </table>
        HTML;
    }
}
