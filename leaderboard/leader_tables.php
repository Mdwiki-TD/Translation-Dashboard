<?PHP

namespace Leaderboard\LeaderTables;

/*
Usage:

use function Leaderboard\LeaderTables\makeSqlQuery;
use function Leaderboard\LeaderTables\createNumbersTable;
use function Leaderboard\LeaderTables\makeUsersTable;
use function Leaderboard\LeaderTables\makeLangTable;

*/

include_once __DIR__ . '/camps.php';

use function Actions\MdwikiSql\fetch_query;
use function Actions\Html\make_modal_fade;

$year = $_REQUEST['year'] ?? 'all';
$camp = $_REQUEST['camp'] ?? 'all';
$project = $_REQUEST['project'] ?? 'all';

$tab_for_graph2 = [
    "year" => $year,
    "campaign" => $camp,
    "user_group" => $project
];

if ($camp == 'all' && isset($_REQUEST['cat'])) {
    $camp = $cat_to_camp[$_REQUEST['cat']] ?? 'all';
}
$camp_cat = $camp_to_cat[$camp] ?? '';

function makeSqlQuery()
{
    global $year, $camp, $project, $camp_cat;
    $queryPart1Group = "SELECT p.title,
        p.target, p.cat, p.lang, p.word, YEAR(p.pupdate) AS pup_y, p.user, u.user_group, LEFT(p.pupdate, 7) as m
        FROM pages p, users u
    ";

    $queryPart1 = "SELECT p.title,
        p.target, p.cat, p.lang, p.word, YEAR(p.pupdate) AS pup_y, LEFT(p.pupdate, 7) as m,
        p.user,
        (SELECT u.user_group FROM users u WHERE p.user = u.username) AS user_group
        FROM pages p
    ";

    $queryPart2 = "
        WHERE p.target != ''
    ";
    // 2023-08-22
    // if ($camp != 'all' && !empty($camp_cat)) $queryPart2 .= "AND p.cat = '$camp_cat' \n";

    if ($year != 'all') {
        $queryPart2 .= "AND YEAR(p.pupdate) = '$year' \n";
    }

    if ($project != 'all') {
        $queryPart1 = $queryPart1Group;
        $queryPart2 .= "AND p.user = u.username \n";
        $queryPart2 .= "AND u.user_group = '$project' \n";
    }

    $query = $queryPart1 . $queryPart2;

    if (isset($_REQUEST['test'])) {
        echo $query;
    }
    return $query;
}
//---
$qua_all = makeSqlQuery();

$Words_total = 0;
$Articles_numbers = 0;
$global_views = 0;
$sql_users_tab_to_lang = array();
$sql_users_tab = array();
$Users_word_table = array();
$sql_Languages_tab = array();
$all_views_by_lang = array();
$Views_by_users = array();

$Views_by_lang_target = make_views_by_lang_target();
$tab_for_graph = [];
// $articles_to_camps, $camps_to_articles

foreach (fetch_query($qua_all) as $Key => $teb) {
    $title  = $teb['title'] ?? "";
    //---
    // 2023-08-22
    if ($camp != 'all' && !empty($camp_cat)) {
        if (!in_array($title, $camps_to_articles[$camp])) continue;
    }
    //---
    $month  = $teb['m'] ?? ""; // 2021-05
    //---
    if (!isset($tab_for_graph[$month])) $tab_for_graph[$month] = 0;
    $tab_for_graph[$month] += 1;
    //---
    $cat    = $teb['cat'] ?? "";
    $lang   = $teb['lang'] ?? "";
    $user   = $teb['user'] ?? "";
    $target = $teb['target'] ?? "";
    $word   = $teb['word'] ?? "";
    if ($word == 0) {
        $word = $Words_table[$title] ?? 0;
    }
    $coco = $Views_by_lang_target[$lang][$target][$year] ?? 0;

    $Words_total += $word;
    $Articles_numbers += 1;
    $global_views += $coco;

    if (!isset($all_views_by_lang[$lang])) $all_views_by_lang[$lang] = 0;
    $all_views_by_lang[$lang] += $coco;

    if (!isset($sql_Languages_tab[$lang])) $sql_Languages_tab[$lang] = 0;
    $sql_Languages_tab[$lang] += 1;

    if (!isset($Users_word_table[$user])) $Users_word_table[$user] = 0;
    $Users_word_table[$user] += $word;

    if (!isset($Views_by_users[$user])) $Views_by_users[$user] = 0;
    $Views_by_users[$user] += $coco;

    if (!isset($sql_users_tab[$user])) $sql_users_tab[$user] = 0;
    $sql_users_tab[$user] += 1;

    if (!isset($sql_users_tab_to_lang[$user])) $sql_users_tab_to_lang[$user] = [];
    if (!isset($sql_users_tab_to_lang[$user][$lang])) $sql_users_tab_to_lang[$user][$lang] = 0;
    $sql_users_tab_to_lang[$user][$lang] += 1;
}

// sort $sql_users_tab_to_lang by numbers

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
function makeUsersTable($min = 2)
{

    global $sql_users_tab, $Users_word_table, $Views_by_users, $sql_users_tab_to_lang;
    $usrse = [];
    $text = <<<HTML
    <table class='table compact table-striped sortable' style='margin-top: 0px !important;margin-bottom: 0px !important'>
        <thead>
            <tr>
                <th class="spannowrap">#</th>
                <th class="spannowrap">User</th>
                <th>Number</th>
                <th>Words</th>
                <th>Pageviews</th>
            </tr>
        </thead>
        <tbody>
    HTML;
    //---

    arsort($sql_users_tab);

    $numb = 0;

    foreach ($sql_users_tab as $user => $usercount) {
        // if ($usercount < $min && $numb > 15) continue;
        $numb += 1;
        $usercount = number_format($usercount);
        $usrse[$user] = isset($sql_users_tab_to_lang[$user]) ? $sql_users_tab_to_lang[$user] : [];
        $views = isset($Views_by_users[$user]) ? number_format($Views_by_users[$user]) : 0;
        $words = isset($Users_word_table[$user]) ? number_format($Users_word_table[$user]) : 0;

        $use = rawurlEncode($user);
        $use = str_replace('+', '_', $use);

        $text .= <<<HTML
            <tr>
                <td>$numb</td>
                <td><a href='leaderboard.php?user=$use'>$user</a></td>
                <td>$usercount</td>
                <td>$words</td>
                <td>$views</td>
            </tr>
            HTML;
    };

    $text .= <<<HTML
        </tbody>
        <tfoot>

        </tfoot>
    </table>
    HTML;
    $lal = "<textarea cols='55' rows='10' id='users_targets' name='users_targets'>";
    foreach ($usrse as $user => $langs) {
        // sort $langs by numbers
        arsort($langs);
        // get first item in $langs
        $lan = array_keys($langs)[0];
        $lal .= "#{{#target:User:$user|$lan.wikipedia.org}}
";
    }
    //---
    $lal .= '</textarea>';
    $lal .= <<<HTML
        <script>
            function copy_targets() {
                let textarea = document.getElementById("users_targets");
                textarea.select();
                document.execCommand("copy");
            }
        </script>
    HTML;
    //---
    $modal = make_modal_fade('', $lal, 'targets', '<a class="btn btn-outline-primary" onclick="copy_targets()">Copy</a>');
    //---
    $text .= $modal;
    //---
    return $text;
}
function makeLangTable()
{

    global $lang_code_to_en, $sql_Languages_tab, $all_views_by_lang;

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

        // $langname = isset($lang_code_to_en[$langcode]) ? "($langcode) " . $lang_code_to_en[$langcode] : $langcode;
        // $langname = isset($lang_code_to_en[$langcode]) ? "<span data-toggle='tooltip' title='$lang_code_to_en[$langcode]'>$langcode</span>" : $langcode;
        $langname = isset($lang_code_to_en[$langcode]) ? "<span data-toggle='tooltip' title='$langcode'>$lang_code_to_en[$langcode]</span>" : $langcode;

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
