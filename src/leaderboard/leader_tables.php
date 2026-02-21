<?PHP

namespace Leaderboard\LeaderTables;

/*
Usage:

use function Leaderboard\LeaderTables\createNumbersTable;
use function Leaderboard\LeaderTables\makeLangTable;

*/

use Tables\Main\MainTables;

function createNumbersTable($c_user, $c_articles, $c_words, $c_langs, $c_views)
{
    $Numbers_table = <<<HTML
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

    return $Numbers_table;
};

function makeLangTable($lang_table)
{
    // ---
    // sort new_data by [lang][count]
    uasort($lang_table, function ($a, $b) {
        return $b["count"] <=> $a["count"];
    });
    // ---
    $addcat = getenv('APP_ENV') !== 'production' && (isset($_GET['nocat']));

    $cac = ($addcat == true) ? '<th>cat</th>' : '';

    $text = <<<HTML
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
    HTML;

    $numb = 0;

    foreach ($lang_table as $langcode => $tab) {
        $comp = $tab['count'];
        $views = $tab['views'];
        $langname = $tab['lang_name'] ?? MainTables::$x_Langs_table[$langcode]['name'] ?? $langcode;
        # Get the Articles numbers

        if ($comp < 1) continue;
        $comp = number_format($comp);
        $numb++;
        // ---
        $view = number_format($views);
        // ---
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
    };

    $text .= <<<HTML
        </tbody>
        </table>
    HTML;

    return $text;
}
