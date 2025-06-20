<?PHP

namespace Leaderboard\Langs;

//---
use Tables\Langs\LangsTables;
use function Leaderboard\Subs\LeadHelp\make_table_lead;
use function Leaderboard\Subs\SubLangs\get_langs_tables;
use function Leaderboard\SubGraph\graph_data_new;
use function Leaderboard\Subs\FilterForm\lead_row;
//---
$mainlang = $_GET['langcode'] ?? "";
$mainlang = rawurldecode(str_replace('_', ' ', $mainlang));
//---
$year_y = $_GET['year'] ?? 'All';
$camp   = $_GET['camp'] ?? 'All';
//---
$langname = LangsTables::$L_code_to_lang_name[$mainlang] ?? $mainlang;
//---
$u_tables = get_langs_tables($mainlang, $year_y);
//---
$dd = $u_tables['dd'];
$dd_Pending = $u_tables['dd_Pending'];
$table_of_views = $u_tables['table_of_views'];
//---
$count_new = count($dd);
//---
[$table1, $main_table] = make_table_lead(
    $dd,
    $tab_type = 'translations',
    $views_table = $table_of_views,
    $page_type = 'langs',
    $user = '',
    $lang = $mainlang
);
//---
$man = $langname;
//---
$cat_link = "";
//---
if ($_SERVER['SERVER_NAME'] == 'localhost' || (isset($_REQUEST['test']) || isset($_COOKIE['test']))) {
    $cat_link = '<br><a target="_blank" href="http://' . $mainlang . '.wikipedia.org/wiki/Category:Translated_from_MDWiki">(cat)</a>';
};
//---
$graph = graph_data_new($dd);
//---
$filter_data = ["user" => "", "lang" => $mainlang, "year" => $year_y, "camp" => $camp];
//---
echo lead_row($table1, $graph, "<h4 class='text-center'>Language: $man ($mainlang) $cat_link</h4>", $filter_data, "lang");
//---
echo <<<HTML
    <div class='card mt-1'>
        <div class='card-body p-1'>
            $main_table
        </div>
    </div>
HTML;
//---
[$_, $table_pnd] = make_table_lead($dd_Pending, $tab_type = 'pending', $page_type = 'langs', $user = '', $lang = $mainlang);
//---
echo <<<HTML
    <br>
    <div class='card'>
        <div class='card-body' style='padding:5px 0px 5px 5px;'>
            <h2 class='text-center'>Translations in process</h2>
            $table_pnd
        </div>
    </div>
HTML;
