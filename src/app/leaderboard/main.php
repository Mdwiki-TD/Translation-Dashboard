<?PHP

namespace App\Leaderboard\Index;

use function App\Render\Html\makeColSm4;
use function App\Render\Html\makeCol;
use function App\Leaderboard\Graph\print_graph_for_table;
use function App\Leaderboard\LeaderTables\createNumbersTable;
use function App\Leaderboard\LeaderTables\makeLangTable;
use function App\Leaderboard\LeaderTabUsers\makeUsersTable;
use function App\Leaderboard\LeaderTabUsers\module_copy_data;
use function App\Leaderboard\Filter\leaderboard_filter;
use function App\SQLorAPI\TopData\get_td_or_sql_top_lang_of_users;
use function App\SQLorAPI\TopData\get_td_or_sql_top_langs;
use function App\SQLorAPI\TopData\get_td_or_sql_top_users;
use function App\SQLorAPI\TopData\get_td_or_sql_status;
use function App\SQLorAPI\GetDataTab\get_camps_to_cat;

function print_cat_table(
    $year,
    $user_group,
    $camp,
    $cat,
    $langs_data,
    $addcat,
    $month
): string {

    $users = get_td_or_sql_top_users($year, $user_group, $cat, $month);

    $lang_table = get_td_or_sql_top_langs($year, $user_group, $cat, $month);

    $articles_all = number_format(array_sum(array_column($users, 'count')));

    // sum all $users[user]["words"] values
    $all_Words = number_format(array_sum(array_column($users, 'words')));

    $all_views = number_format(array_sum(array_column($users, 'views')));

    $numbersTable = createNumbersTable(
        count($users),
        $articles_all,
        $all_Words,
        count($lang_table),
        $all_views
    );

    $graph_data = get_td_or_sql_status($year, $user_group, $cat);

    $graph_html = print_graph_for_table($graph_data, $no_card = false);


    $numbersCol = makeCol('Numbers', $numbersTable, $graph_html);

    $usersTable = makeUsersTable($users);

    $users = array_keys($users);

    $users_tab = get_td_or_sql_top_lang_of_users($users);

    $copy_module = module_copy_data($users_tab);

    $modal_a = <<<HTML
        <button type="button" class="btn-tool" href="#" data-bs-toggle="modal" data-bs-target="#targets">
            <i class="fas fa-copy"></i>
        </button>
    HTML;

    $usersCol = makeColSm4('Top users by number of translation', $usersTable, 5, $copy_module, $modal_a);

    $languagesTable = makeLangTable($lang_table, $langs_data, $addcat);
    $languagesCol = makeColSm4('Top languages by number of Articles', $languagesTable, 4);

    return <<<HTML
        <div class="row g-3">
            $numbersCol
            $usersCol
            $languagesCol
        </div>
    HTML;
}

function main_leaderboard($year, $camp, $user_group, $langs_data, $addcat, $month): string
{

    $s_camp_to_cat = get_camps_to_cat();

    $cat = $s_camp_to_cat[$camp] ?? '';

    $filter_form = leaderboard_filter($year, $month, $user_group, $camp);

    $uux = print_cat_table($year, $user_group, $camp, $cat, $langs_data, $addcat, $month);

    $board = <<<HTML
        $filter_form
        <hr/>
        <div class="container-fluid">
            $uux
        </div>
    HTML;

    return $board;
}
