<?PHP

namespace Leaderboard\Index;

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

/*

Usage:

use function Leaderboard\Index\main_leaderboard;

*/

use Tables\SqlTables\TablesSql;
use function Actions\Html\makeColSm4;
use function Actions\Html\makeCol;
use function Leaderboard\Graph\print_graph_for_table;
use function Leaderboard\LeaderTables\createNumbersTable;
use function Leaderboard\LeaderTables\makeLangTable;
use function Leaderboard\LeaderTabUsers\makeUsersTable;
use function Leaderboard\LeaderTabUsers\module_copy_data;
use function Leaderboard\Filter\leaderboard_filter;
use function SQLorAPI\TopData\get_td_or_sql_top_lang_of_users;
use function SQLorAPI\TopData\get_td_or_sql_top_langs;
use function SQLorAPI\TopData\get_td_or_sql_top_users;
use function SQLorAPI\TopData\get_td_or_sql_status;

function print_cat_table($year, $user_group, $camp, $cat): string
{
    // ---
    $users_list = get_td_or_sql_top_users($year, $user_group, $cat);
    // ---
    $lang_table = get_td_or_sql_top_langs($year, $user_group, $cat);
    // ---
    $all_articles = number_format(array_sum(array_column($users_list, 'count')));
    // ---
    // sum all $users_list[user]["words"] values
    $all_Words = number_format(array_sum(array_column($users_list, 'words')));
    // ---
    $all_views = number_format(array_sum(array_column($users_list, 'views')));
    // ---
    $numbersTable = createNumbersTable(
        count($users_list),
        $all_articles,
        $all_Words,
        count($lang_table),
        $all_views
    );
    //---
    $graph_data = get_td_or_sql_status($year, $user_group, $cat);
    //---
    $graph_html = print_graph_for_table($graph_data, $no_card = false);
    //---

    $numbersCol = makeCol('Numbers', $numbersTable, $graph_html);

    $usersTable = makeUsersTable($users_list);

    $users = array_keys($users_list);

    $users_tab = get_td_or_sql_top_lang_of_users($users);

    $copy_module = module_copy_data($users_tab);

    $modal_a = <<<HTML
        <button type="button" class="btn-tool" href="#" data-bs-toggle="modal" data-bs-target="#targets">
            <i class="fas fa-copy"></i>
        </button>
    HTML;
    //---
    $usersCol = makeColSm4('Top users by number of translation', $usersTable, 5, $copy_module, $modal_a);
    //---
    $languagesTable = makeLangTable($lang_table);
    $languagesCol = makeColSm4('Top languages by number of Articles', $languagesTable, 4);

    return <<<HTML
        <div class="row g-3">
            $numbersCol
            $usersCol
            $languagesCol
        </div>
    HTML;
}

function main_leaderboard()
{
    //---
    $year       = $_GET['year'] ?? 'all';
    $camp       = $_GET['camp'] ?? 'all';
    $user_group    = $_GET['project'] ?? $_GET['user_group'] ?? 'all';
    //---
    $filter_form = leaderboard_filter($year, $user_group, $camp);
    //---
    $cat = TablesSql::$s_camp_to_cat[$camp] ?? '';
    //---
    $uux = print_cat_table($year, $user_group, $camp, $cat);
    //---
    $board = <<<HTML
        $filter_form
        <hr/>
        <div class="container-fluid">
            $uux
        </div>
    HTML;
    //---
    return $board;
}
