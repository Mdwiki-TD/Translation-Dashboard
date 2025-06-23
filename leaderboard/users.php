<?PHP

namespace Leaderboard\Users;

/*
Usage:

use function Leaderboard\Users\users_html;

*/

use function Leaderboard\Subs\LeadHelp\make_users_lead;
use function Actions\Html\make_mdwiki_user_url;
use function Leaderboard\Subs\SubUsers\get_users_tables;
use function Leaderboard\SubGraph\graph_data_new;
use function Leaderboard\Subs\FilterForm\lead_row;

function users_html()
{
    $output = '';
    //---
    $mainlang = $_GET['lang'] ?? 'All';
    $mainlang = rawurldecode(str_replace('_', ' ', $mainlang));
    //---
    $mainuser = $_GET['user'] ?? "";
    //---
    $year_y = $_GET['year'] ?? 'All';
    $camp   = $_GET['camp'] ?? 'All';
    //---
    $u_tables = get_users_tables($mainuser, $year_y, $mainlang);
    //---
    $dd = $u_tables['dd'];
    $dd_Pending = $u_tables['dd_Pending'];
    $table_of_views = $u_tables['table_of_views'];
    //---
    $count_new = count($dd);
    //---
    [$table1, $main_table] = make_users_lead($dd, 'translations', $table_of_views, $mainuser);
    //---
    $man = make_mdwiki_user_url($mainuser);
    //---
    $graph = graph_data_new($dd);
    //---
    $filter_data = ["user" => $mainuser, "lang" => $mainlang, "year" => $year_y, "camp" => $camp];
    //---
    $output .= lead_row($table1, $graph, "<h4 class='text-center'>User: $man</h4>", $filter_data, "user");
    //---
    $output .= <<<HTML
        <div class='card mt-1'>
            <div class='card-body p-1'>
                $main_table
            </div>
        </div>
    HTML;
    //---
    [$_, $table_pnd] = make_users_lead($dd_Pending, 'pending', $table_of_views, $mainuser);
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
