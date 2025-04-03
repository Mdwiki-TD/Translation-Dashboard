<?PHP

namespace Leaderboard\LeaderTables;

/*
Usage:

use function Leaderboard\LeaderTables\createNumbersTable;
use function Leaderboard\LeaderTables\makeLangTable;

*/

include_once __DIR__ . '/camps.php';
// include_once __DIR__ . '/leader_tables_tabs.php';

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

function makeLangTable()
{

    global $sql_Languages_tab, $all_views_by_lang, MainTables::$x_Langs_table;

    arsort($sql_Languages_tab);

    $addcat = $_SERVER['SERVER_NAME'] == 'localhost' && (isset($_REQUEST['nocat']));

    $cac = ($addcat == true) ? '<th>cat</th>' : '';

    $text = <<<HTML
    <table class='table compact table-striped sortable' style='margin-top: 0px !important;margin-bottom: 0px !important'>
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

    foreach ($sql_Languages_tab as $langcode => $comp) {

        # Get the Articles numbers

        if ($comp < 1) continue;
        $comp = number_format($comp);
        $numb++;
        // ---
        $na = MainTables::$x_Langs_table[$langcode]['name'] ?? "";
        // ---
        $langname = ($na != "") ? "<span data-toggle='tooltip' title='$langcode'>$na</span>" : $langcode;
        // ---
        $view = number_format($all_views_by_lang[$langcode]) ?? 0;
        $cach = <<<HTML
            <td><a target="_blank" href="https://$langcode.wikipedia.org/wiki/Category:Translated_from_MDWiki">cat</a></td>
        HTML;
        if ($addcat != true) $cach = '';

        $text .= <<<HTML
            <tr>
                <td>$numb</td>
                <td><a href='leaderboard.php?langcode=$langcode'>$langname</a></td>
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
