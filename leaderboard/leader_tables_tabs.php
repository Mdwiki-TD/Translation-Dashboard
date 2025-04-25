<?PHP

namespace Leaderboard\Tabs;

include_once __DIR__ . '/../api_or_sql/index.php';
/*
(\$)(tab_for_graph2|tab_for_graph|Words_total|Articles_numbers|global_views|sql_users_tab|Users_word_table|sql_Languages_tab|all_views_by_lang|Views_by_users)\b

LeaderBoardTabs::$1u_$2

Usage:
use Leaderboard\Tabs\LeaderBoardTabs;

*/

use Tables\Main\MainTables;
use Leaderboard\Camps\CampsTabs;
use Tables\SqlTables\TablesSql;
use function SQLorAPI\GetLead\get_leaderboard_table;
// use function Tables\SqlTables\make_views_by_lang_target;

class LeaderBoardTabs
{
    public static $u_tab_for_graph = [];
    public static $u_tab_for_graph2 = [];
    public static $u_Words_total = 0;
    public static $u_Articles_numbers = 0;
    public static $u_global_views = 0;
    public static $u_sql_users_tab = [];
    public static $u_Users_word_table = [];
    public static $u_sql_Languages_tab = [];
    public static $u_all_views_by_lang = [];
    public static $u_Views_by_users = [];
}

$year = $_REQUEST['year'] ?? 'all';
$camp = $_REQUEST['camp'] ?? 'all';
$project = $_REQUEST['project'] ?? 'all';
$langcode = $_REQUEST['langcode'] ?? '';

LeaderBoardTabs::$u_tab_for_graph2 = [
    "year" => $year,
    "campaign" => $camp,
    "user_group" => $project
];

if ($camp == 'all' && isset($_REQUEST['cat'])) {
    $camp = TablesSql::$s_cat_to_camp[$_REQUEST['cat']] ?? 'all';
}
$camp_cat = TablesSql::$s_camp_to_cat[$camp] ?? '';

// $Views_by_lang_target = make_views_by_lang_target($year, $langcode);

$ddde1 = get_leaderboard_table($year, $project, $camp_cat);
// ---
// compare_it($ddde, $ddde1);
// ---
$campsto_articles = CampsTabs::$camps_to_articles;
// ---
foreach ($ddde1 as $Key => $teb) {
    $title  = $teb['title'] ?? "";
    $cat    = $teb['cat'] ?? "";
    //---
    if (!empty($camp_cat) && !empty($cat) && $cat != $camp_cat) continue;
    //---
    // 2023-08-22
    if ($camp != 'all' && !empty($camp_cat) && $cat == "") {
        if (!empty($campsto_articles[$camp]) && !in_array($title, $campsto_articles[$camp])) continue;
    }
    //---
    $month  = $teb['m'] ?? ""; // 2021-05
    //---
    if (!isset(LeaderBoardTabs::$u_tab_for_graph[$month])) LeaderBoardTabs::$u_tab_for_graph[$month] = 0;
    LeaderBoardTabs::$u_tab_for_graph[$month] += 1;
    //---
    $lang   = $teb['lang'] ?? "";
    $user   = $teb['user'] ?? "";
    $target = $teb['target'] ?? "";
    $word   = $teb['word'] ?? 0;
    // ---
    // if $word is number and not int do (int)$word; else 0
    $word = (int)$word ?? 0;
    // ---
    if ($word == 0) {
        $word = MainTables::$x_Words_table[$title] ?? 0;
    }
    // ---
    $views = $teb['views'] ?? 0;
    // ---
    // $coco = $Views_by_lang_target[$lang][$target] ?? 0;
    // if ($views != $coco) echo "Views ($target): tab views: $views  coco: $coco<br>";
    // ---
    LeaderBoardTabs::$u_Words_total += $word;
    LeaderBoardTabs::$u_Articles_numbers += 1;
    LeaderBoardTabs::$u_global_views += $views;

    if (!isset(LeaderBoardTabs::$u_all_views_by_lang[$lang])) LeaderBoardTabs::$u_all_views_by_lang[$lang] = 0;
    LeaderBoardTabs::$u_all_views_by_lang[$lang] += $views;

    if (!isset(LeaderBoardTabs::$u_sql_Languages_tab[$lang])) LeaderBoardTabs::$u_sql_Languages_tab[$lang] = 0;
    LeaderBoardTabs::$u_sql_Languages_tab[$lang] += 1;

    if (!isset(LeaderBoardTabs::$u_Users_word_table[$user])) LeaderBoardTabs::$u_Users_word_table[$user] = 0;
    LeaderBoardTabs::$u_Users_word_table[$user] += $word;

    if (!isset(LeaderBoardTabs::$u_Views_by_users[$user])) LeaderBoardTabs::$u_Views_by_users[$user] = 0;
    LeaderBoardTabs::$u_Views_by_users[$user] += $views;

    if (!isset(LeaderBoardTabs::$u_sql_users_tab[$user])) LeaderBoardTabs::$u_sql_users_tab[$user] = 0;
    LeaderBoardTabs::$u_sql_users_tab[$user] += 1;
}


arsort(LeaderBoardTabs::$u_sql_users_tab);
