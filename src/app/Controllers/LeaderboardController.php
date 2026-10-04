<?php

namespace App\Controllers;

use App\User\CurrentUser;
use App\Settings;
use App\Leaderboard\MainLeaderboard;
use App\Leaderboard\LangsLeaderboard;
use App\Leaderboard\UsersLeaderboard;

use App\SQLorAPI\ViewsTable;

use function App\Leaderboard\Helpers\Graph\Graph\print_graph_tab;
use function App\Leaderboard\Helpers\Graph\Graph2\print_graph_tab_2_new;
use function App\Leaderboard\Helpers\Camps\CampsText\echo_html;

use App\SQLorAPI\CategoriesTable;
use App\SQLorAPI\TitlesTable;

class LeaderboardController
{
    private CurrentUser $currentUser;

    public function __construct(?CurrentUser $currentUser = null)
    {
        $this->currentUser = $currentUser ?? CurrentUser::getInstance();
    }

    public function handleRequest(): void
    {
        $global_username = $this->currentUser->getUsername();

        $get = filter_input(INPUT_GET, 'get', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

        $langcode = filter_input(INPUT_GET, 'langcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
        $mainlang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';

        $user_to_html = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
        $user_to_curl = filter_input(INPUT_GET, 'user', FILTER_UNSAFE_RAW) ?? '';

        $year_y   = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';
        $month_y  = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
        $camp     = filter_input(INPUT_GET, 'camp', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';

        $_titles_infos   = (TitlesTable::getInstance())->getTitlesInfos();
        $categories_tab = (CategoriesTable::getInstance())->getCategories();

        $lead_words_table = array_column($_titles_infos, 'w_lead_words', 'title');
        $cats_data = array_column($categories_tab, "campaign", "category");

        if ($get == 'users' || !empty($user_to_curl)) {

            echo (new UsersLeaderboard())->render(
                $mainlang,
                $year_y,
                $camp,
                $user_to_curl,
                $user_to_html,
                $global_username,
                $lead_words_table,
                $cats_data
            );
        } elseif ($get == 'langs' || !empty($langcode)) {

            echo (new LangsLeaderboard())->render(
                $langcode,
                $year_y,
                $camp,
                $lead_words_table,
                $cats_data
            );
        } elseif (!empty($_GET['camps'] ?? '')) {
            // http://localhost:9001/Translation_Dashboard/leaderboard.php?camps=1

            echo echo_html();
        } elseif (!empty($_GET['graph'] ?? '')) {
            // http://localhost:9001/Translation_Dashboard/leaderboard.php?graph=1

            $data = (ViewsTable::getInstance())->getGraphData();
            echo print_graph_tab($data);
        } elseif (!empty($_GET['graph_api'] ?? '')) {
            // http://localhost:9001/Translation_Dashboard/leaderboard.php?graph_api=1

            echo print_graph_tab_2_new();
        } else {

            $user_group = filter_input(INPUT_GET, 'project', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                ?? filter_input(INPUT_GET, 'user_group', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                ?? 'all';

            $langs_data = (TitlesTable::getInstance())->getLangs();

            $settings = Settings::getInstance();
            $addcat = !$settings->isProduction() && (isset($_GET['nocat']));

            $controller = new MainLeaderboard();
            echo $controller->render($year_y, $camp, $user_group, $langs_data, $addcat, $month_y);
        }
    }
}
