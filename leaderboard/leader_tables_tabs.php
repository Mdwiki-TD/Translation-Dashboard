<?PHP

namespace Leaderboard\Tabs;

// include_once __DIR__ . '/../api_or_sql/index.php';
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
    public static $u_sql_Languages_tab = [];
    public static $u_all_views_by_lang = [];

    public static $tab_users_new = [];
}

$year     = $_GET['year'] ?? 'all';
$camp     = $_GET['camp'] ?? 'all';
$user_group  = $_GET['project'] ?? $_GET['user_group'] ?? 'all';
$langcode = $_GET['langcode'] ?? '';

LeaderBoardTabs::$u_tab_for_graph2 = [
    "year" => $year,
    "campaign" => $camp,
    "user_group" => $user_group
];

if ($camp == 'all' && isset($_GET['cat'])) {
    $camp = TablesSql::$s_cat_to_camp[$_GET['cat']] ?? 'all';
}
$camp_cat = TablesSql::$s_camp_to_cat[$camp] ?? '';

// $Views_by_lang_target = make_views_by_lang_target($year, $langcode);

$ddde1 = get_leaderboard_table($year, $user_group, $camp_cat);
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
    if (!isset(LeaderBoardTabs::$u_tab_for_graph[$month])) {
        LeaderBoardTabs::$u_tab_for_graph[$month] = 0;
    } else {
        LeaderBoardTabs::$u_tab_for_graph[$month] += 1;
    }
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

    if (!isset(LeaderBoardTabs::$u_all_views_by_lang[$lang])) LeaderBoardTabs::$u_all_views_by_lang[$lang] = 0;
    LeaderBoardTabs::$u_all_views_by_lang[$lang] += $views;

    if (!isset(LeaderBoardTabs::$u_sql_Languages_tab[$lang])) LeaderBoardTabs::$u_sql_Languages_tab[$lang] = 0;
    LeaderBoardTabs::$u_sql_Languages_tab[$lang] += 1;

    // ---
    if (!isset(LeaderBoardTabs::$tab_users_new[$user])) {
        LeaderBoardTabs::$tab_users_new[$user] = [
            "count" => 1,
            "views" => $views,
            "words" => $word
        ];
    } else {
        LeaderBoardTabs::$tab_users_new[$user]["count"] += 1;
        LeaderBoardTabs::$tab_users_new[$user]["views"] += $views;
        LeaderBoardTabs::$tab_users_new[$user]["words"] += $word;
    }
}

// sort LeaderBoardTabs::$tab_users_new by count
arsort(LeaderBoardTabs::$tab_users_new);
