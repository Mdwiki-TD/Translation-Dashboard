<?php

namespace App\Controllers;

use App\User\CurrentUser;
use App\Settings;
use App\Leaderboard\MainLeaderboard;
use App\Leaderboard\LangsLeaderboard;
use App\Leaderboard\UsersLeaderboard;
use App\Leaderboard\Helpers\Graph\GraphApi;
use App\Leaderboard\Helpers\Camps\CampsText;
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

        $username = filter_input(INPUT_GET, 'user', FILTER_UNSAFE_RAW) ?? '';

        $year_y   = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';
        $month_y  = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
        $camp     = filter_input(INPUT_GET, 'camp', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';

        $_titles_infos   = (TitlesTable::getInstance())->getTitlesInfos();
        $categories_tab = (CategoriesTable::getInstance())->getCategories();

        $lead_words_table = array_column($_titles_infos, 'w_lead_words', 'title');
        $cats_data = array_column($categories_tab, "campaign", "category");

        if ($get == 'users' || !empty($username)) {

            // Pass the username to the constructor
            $usersLeaderboard = new UsersLeaderboard($username, $mainlang);

            echo $usersLeaderboard->render(
                $year_y,
                $camp,
                $global_username,
                $lead_words_table,
                $cats_data
            );
        } elseif ($get == 'langs' || !empty($langcode)) {
            // Pass the language code directly to the constructor
            $langsLeaderboard = new LangsLeaderboard($langcode);

            echo $langsLeaderboard->render(
                $year_y,
                $camp,
                $lead_words_table,
                $cats_data
            );
        } elseif (!empty($_GET['camps'] ?? '')) {
            // Example endpoint: http://localhost:9001/Translation_Dashboard/leaderboard.php?camps=1

            CampsText::echo_html();
        } elseif (!empty($_GET['graph_api'] ?? '')) {
            // Example endpoint: http://localhost:9001/Translation_Dashboard/leaderboard.php?graph_api=1

            echo GraphApi::renderGraph();
        } else {

            $user_group = filter_input(INPUT_GET, 'project', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                ?? filter_input(INPUT_GET, 'user_group', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                ?? 'all';

            $langs_data = (TitlesTable::getInstance())->getLangs();

            $settings = Settings::getInstance();
            $addcat = !$settings->isProduction() && (isset($_GET['nocat']));

            // Initialize MainLeaderboard with options passed to constructor
            $controller = new MainLeaderboard(
                $year_y,
                $camp,
                $user_group,
                $month_y
            );
            echo $controller->render($langs_data, $addcat);
        }
    }
}
