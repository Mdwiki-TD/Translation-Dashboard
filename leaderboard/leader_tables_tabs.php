<?PHP

include_once __DIR__ . '/../api_or_sql/index.php';

use function SQLorAPI\GetLead\get_leaderboard_table;
use function Tables\SqlTables\make_views_by_lang_target;

$year = $_REQUEST['year'] ?? 'all';
$camp = $_REQUEST['camp'] ?? 'all';
$project = $_REQUEST['project'] ?? 'all';
$langcode = $_REQUEST['langcode'] ?? '';

$tab_for_graph2 = [
    "year" => $year,
    "campaign" => $camp,
    "user_group" => $project
];

if ($camp == 'all' && isset($_REQUEST['cat'])) {
    $camp = $cat_to_camp[$_REQUEST['cat']] ?? 'all';
}
$camp_cat = $camp_to_cat[$camp] ?? '';

$Words_total = 0;
$Articles_numbers = 0;
$global_views = 0;
$sql_users_tab = array();
$Users_word_table = array();
$sql_Languages_tab = array();
$all_views_by_lang = array();
$Views_by_users = array();

$Views_by_lang_target = make_views_by_lang_target($year, $langcode);
$tab_for_graph = [];
// $articles_to_camps, $camps_to_articles

$ddde1 = get_leaderboard_table($year, $project, $camp_cat);
// ---
// compare_it($ddde, $ddde1);
// ---
foreach ($ddde1 as $Key => $teb) {
    $title  = $teb['title'] ?? "";
    $cat    = $teb['cat'] ?? "";
    //---
    if (!empty($camp_cat) && !empty($cat) && $cat != $camp_cat) continue;
    //---
    // 2023-08-22
    if ($camp != 'all' && !empty($camp_cat) && $cat == "") {
        if (!empty($camps_to_articles[$camp]) && !in_array($title, $camps_to_articles[$camp])) continue;
    }
    //---
    $month  = $teb['m'] ?? ""; // 2021-05
    //---
    if (!isset($tab_for_graph[$month])) $tab_for_graph[$month] = 0;
    $tab_for_graph[$month] += 1;
    //---
    $lang   = $teb['lang'] ?? "";
    $user   = $teb['user'] ?? "";
    $target = $teb['target'] ?? "";
    $word   = $teb['word'] ?? "";
    if ($word == 0) {
        $word = $Words_table[$title] ?? 0;
    }
    // $coco = $Views_by_lang_target[$lang][$target][$year] ?? 0;
    $coco = $Views_by_lang_target[$lang][$target] ?? 0;

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
}


arsort($sql_users_tab);
