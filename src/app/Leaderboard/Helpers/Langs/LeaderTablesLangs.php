<?php

namespace App\Leaderboard\Helpers\Langs;

class LeaderTablesLangs
{
    private array $data;

    public function __construct(array $data)
    {
        // sort data by [item][count]
        uasort($data, function ($a, $b): int {
            return $b["count"] <=> $a["count"];
        });

        $this->data = $data;
    }
    public function makeLangTable(bool $addcat): string
    {

        $numb = 0;
        $text = "";
        $cac = ($addcat == true) ? '<th>cat</th>' : '';

        foreach ($this->data as $langcode => $tab) {
            $comp = $tab['count'];
            $views = $tab['views'];
            $langname = $tab['lang_name'] ?? $langcode;

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
