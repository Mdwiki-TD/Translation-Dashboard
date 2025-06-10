<?PHP

namespace Leaderboard\Langs;

//---
//---
use Tables\Langs\LangsTables;
use function Leaderboard\Subs\LeadHelp\make_table_lead;
use function Leaderboard\Subs\FilterForm\make_filter_form_langs;
use function Leaderboard\Subs\SubLangs\get_langs_tables;
use function Leaderboard\SubGraph\graph_data_new;
//---
$mainlang = $_GET['langcode'] ?? "";
$mainlang = rawurldecode(str_replace('_', ' ', $mainlang));
//---
$year_y = $_GET['year'] ?? 'All';
//---
$langname = LangsTables::$L_code_to_lang[$mainlang] ?? $mainlang;
//---
$u_tables = get_langs_tables($mainlang, $year_y);
//---
$dd = $u_tables['dd'];
$dd_Pending = $u_tables['dd_Pending'];
$table_of_views = $u_tables['table_of_views'];
//---
krsort($dd);
//---
$count_new = count($dd);
//---
$tat = make_table_lead(
    $dd,
    $tab_type = 'translations',
    $views_table = $table_of_views,
    $page_type = 'langs',
    $user = '',
    $lang = $mainlang
);
//---
$table1 = $tat['table1'];
$table2 = $tat['table2'];
//---
$man = $langname;
//---
if ($_SERVER['SERVER_NAME'] == 'localhost' || (isset($_REQUEST['test']) || isset($_COOKIE['test']))) {
    $man .= ' <a target="_blank" href="http://' . $mainlang . '.wikipedia.org/wiki/Category:Translated_from_MDWiki">(cat)</a>';
};
//---
$filter_form = make_filter_form_langs($mainlang, $year_y);
//---
$graph = graph_data_new($dd, "lang_chart");
//---
echo <<<HTML
    <div class='row content'>
        <div class='col-md-2'>
            $table1
        </div>
        <div class='col-md-4'>
            <div class="position-relative">
                $graph
            </div>
        </div>
        <div class='col-md-3'>
            <h3 class='text-center'>$man ($count_new)</h3>
        </div>
        <div class='col-md-3'>
            $filter_form
        </div>
    </div>
    <div class='card'>
        <div class='card-body' style='padding:5px 0px 5px 5px;'>
            $table2
        </div>
    </div>
HTML;
//---
krsort($dd_Pending);
//---
$table_pnd = make_table_lead($dd_Pending, $tab_type = 'pending', $page_type = 'langs', $user = '', $lang = $mainlang);
//---
$tab_pnd = $table_pnd['table2'];
//---
echo <<<HTML
<br>
<div class='card'>
    <div class='card-body' style='padding:5px 0px 5px 5px;'>
        <h2 class='text-center'>Translations in process</h2>
        $tab_pnd
    </div>
</div>
HTML;
