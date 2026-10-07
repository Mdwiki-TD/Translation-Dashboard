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
        // -----

        $get = filter_input(INPUT_GET, 'get', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

        $langcode = filter_input(INPUT_GET, 'langcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?? filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?? '';

        $username = filter_input(INPUT_GET, 'user', FILTER_UNSAFE_RAW) ?? '';

        $year       = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';
        $month      = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
        $campaign   = filter_input(INPUT_GET, 'camp', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';

        $userGroup = filter_input(INPUT_GET, 'project', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?? filter_input(INPUT_GET, 'user_group', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?? 'all';

        // -----

        $global_username  = $this->currentUser->getUsername();

        $_titles_infos    = (TitlesTable::getInstance())->getTitlesInfos();
        $categories_tab   = (CategoriesTable::getInstance())->getCategories();

        $lead_words_table = array_column($_titles_infos, 'w_lead_words', 'title');
        $cats_data        = array_column($categories_tab, "campaign", "category");

        if ($get == 'users' || !empty($username)) {
            // Pass the username to the constructor
            $usersLeaderboard = new UsersLeaderboard(
                $username,
                $langcode,
                $year,
                $campaign,
            );

            echo $usersLeaderboard->render(
                $global_username,
                $lead_words_table,
                $cats_data
            );
        } elseif ($get == 'langs' || !empty($langcode)) {
            // Pass the language code directly to the constructor
            $langsLeaderboard = new LangsLeaderboard(
                $langcode,
                $year,
                $campaign,
            );

            echo $langsLeaderboard->render(
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

            $langs_data = (TitlesTable::getInstance())->getLangs();

            $settings = Settings::getInstance();
            $addcat = !$settings->isProduction() && (isset($_GET['nocat']));

            // Initialize MainLeaderboard with options passed to constructor
            $controller = new MainLeaderboard(
                $year,
                $campaign,
                $userGroup,
                $month
            );
            echo $controller->render($langs_data, $addcat);
        }
    }
}
