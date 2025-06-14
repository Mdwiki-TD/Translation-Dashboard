<?PHP

namespace Leaderboard\Users;

use function Actions\Html\make_mdwiki_user_url;
use function Leaderboard\Subs\LeadHelp\make_table_lead;
use function Leaderboard\Subs\FilterForm\make_filter_form_users;
use function Leaderboard\Subs\SubUsers\get_users_tables;
use function Leaderboard\SubGraph\graph_data_new;
use function Leaderboard\Subs\FilterForm\lead_row;
//---
$mainuser = $_GET['user'] ?? "";
//---
$year_y = $_GET['year'] ?? 'All';
$lang_y = $_GET['lang'] ?? 'All';
//---
if ($mainuser == $GLOBALS['global_username']) {
    echo '<script>
        $(".navbar-nav").find("li.active").removeClass("active");
        $("#myboard").addClass("active");
        </script>
    ';
};
//---
$u_tables = get_users_tables($mainuser, $year_y, $lang_y);
//---
$dd = $u_tables['dd'];
$dd_Pending = $u_tables['dd_Pending'];
$table_of_views = $u_tables['table_of_views'];
//---
krsort($dd);
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
$filter_form = make_filter_form_users($mainuser, $lang_y, $year_y);
//---
$graph = graph_data_new($dd, "user_chart");
//---
echo lead_row($table1, $graph, "<h4 class='text-center'>User: $man</h4>", $filter_form);
//---
echo <<<HTML
    <div class='card mt-1'>
        <div class='card-body p-1'>
            $main_table
        </div>
    </div>
HTML;
//---
krsort($dd_Pending);
//---
[$_, $table_pnd] = make_table_lead($dd_Pending, $tab_type = 'pending', $views_table = $table_of_views, $page_type = 'users', $user = $mainuser, $lang = '');
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
