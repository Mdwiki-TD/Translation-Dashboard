<?PHP

namespace Leaderboard\Langs;

//---
//---
use function Leaderboard\LeadHelp\make_table_lead;
use function Leaderboard\FilterForm\make_filter_form_langs;
use function Leaderboard\SubLangs\get_langs_tables;
//---
$mainlang = $_REQUEST['langcode'] ?? "";
$mainlang = rawurldecode(str_replace('_', ' ', $mainlang));
//---
$test = $_REQUEST['test'] ?? '';
$year_y = $_REQUEST['year'] ?? 'All';
//---
$langname = $code_to_lang[$mainlang] ?? $mainlang;
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
if ($_SERVER['SERVER_NAME'] == 'localhost' || !empty($test)) {
    $man .= ' <a target="_blank" href="http://' . $mainlang . '.wikipedia.org/wiki/Category:Translated_from_MDWiki">(cat)</a>';
};
//---
$filter_form = make_filter_form_langs($mainlang, $year_y);
//---
echo <<<HTML
    <div class='row content'>
        <div class='col-md-3'>$table1</div>
        <div class='col-md-4'><h2 class='text-center'>$man ($count_new)</h2></div>
        <div class='col-md-5'>$filter_form</div>
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
//---
