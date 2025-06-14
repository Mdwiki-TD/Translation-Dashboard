<?PHP

namespace Leaderboard\Users;

use function Actions\Html\make_mdwiki_user_url;
use function Leaderboard\Subs\LeadHelp\make_table_lead;
use function Leaderboard\Subs\SubUsers\get_users_tables;
use function Leaderboard\SubGraph\graph_data_new;
use function Leaderboard\Subs\FilterForm\lead_row;
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
[$table1, $main_table] = make_table_lead(
    $dd,
    $tab_type = 'translations',
    $views_table = $table_of_views,
    $page_type = 'users',
    $user = $mainuser,
    $lang = ''
);
//---
$man = make_mdwiki_user_url($mainuser);
//---
$graph = graph_data_new($dd);
//---
$filter_data = ["user" => $mainuser, "lang" => $mainlang, "year" => $year_y, "camp" => $camp];
//---
echo lead_row($table1, $graph, "<h4 class='text-center'>User: $man</h4>", $filter_data, "user");
//---
echo <<<HTML
    <div class='card mt-1'>
        <div class='card-body p-1'>
            $main_table
        </div>
    </div>
HTML;
//---
[$_, $table_pnd] = make_table_lead($dd_Pending, $tab_type = 'pending', $views_table = $table_of_views, $page_type = 'users', $user = $mainuser, $lang = '');
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
