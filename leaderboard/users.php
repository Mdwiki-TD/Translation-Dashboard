<?PHP

namespace Leaderboard\Users;

use function Actions\Html\make_mdwiki_user_url;
use function Leaderboard\LeadHelp\make_table_lead;
use function Leaderboard\FilterForm\make_filter_form_users;
use function Leaderboard\SubUsers\get_users_tables;
//---
$mainuser = $_REQUEST['user'] ?? "";
//---
$test = $_REQUEST['test'] ?? '';
$year_y = $_REQUEST['year'] ?? 'All';
$lang_y = $_REQUEST['lang'] ?? 'All';
//---
if ($mainuser == $GLOBALS['global_username']) {
    echo '<script>
        $(".navbar-nav").find("li.active").removeClass("active");
        $("#myboard").addClass("active");
        </script>
    ';
};
//---
$u_tables = get_users_tables($mainuser, $year_y, $lang_y, $test = $test);
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
    $page_type = 'users',
    $user = $mainuser,
    $lang = ''
);
//---
$table1 = $tat['table1'];
$table2 = $tat['table2'];
//---
$man = make_mdwiki_user_url($mainuser);
//---
$filter_form = make_filter_form_users($mainuser, $lang_y, $year_y);
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
$table_pnd = make_table_lead($dd_Pending, $tab_type = 'pending', $views_table = $table_of_views, $page_type = 'users', $user = $mainuser, $lang = '');
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
