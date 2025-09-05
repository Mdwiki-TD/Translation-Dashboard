<?PHP

namespace Leaderboard\Langs;

/*
Usage:

use function Leaderboard\Langs\langs_html;

*/

use Tables\Langs\LangsTables;
use function Leaderboard\Subs\LeadHelp\make_langs_lead;
use function Leaderboard\Subs\SubLangs\get_langs_tables;
use function Leaderboard\SubGraph\graph_data_new;
use function Leaderboard\Subs\FilterForm\lead_row;

function langs_html($mainlang, $year_y, $camp)
{
    $output = '';
    //---
    $mainlang = rawurldecode(str_replace('_', ' ', $mainlang));
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
    [$table1, $main_table] = make_langs_lead($dd, 'translations', $table_of_views, $mainlang);
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
    $output .= lead_row($table1, $graph, "<h4 class='text-center'>Language: $man ($mainlang) $cat_link</h4>", $filter_data, "lang");
    //---
    $output .= <<<HTML
        <div class='card mt-1'>
            <div class='card-body p-1'>
                $main_table
            </div>
        </div>
    HTML;
    //---
    [$_, $table_pnd] = make_langs_lead($dd_Pending, 'pending', $table_of_views, $mainlang);
    //---
    $output .= <<<HTML
        <br>
        <div class='card'>
            <div class='card-body' style='padding:5px 0px 5px 5px;'>
                <h2 class='text-center'>Translations in process</h2>
                $table_pnd
            </div>
        </div>
    HTML;
    // ---
    return $output;
}
