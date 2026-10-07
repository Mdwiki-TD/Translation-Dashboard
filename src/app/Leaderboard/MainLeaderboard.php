<?php

namespace App\Leaderboard;

use App\SQLorAPI\LeaderboardTable;
use App\SQLorAPI\CategoriesTable;
use App\SQLorAPI\TitlesTable;
use App\SQLorAPI\PagesTable;

use App\Utils\Html;
use App\Leaderboard\Helpers\Graph\LangUserGraph;
use App\Leaderboard\Helpers\Langs\LeaderTablesLangs;
use App\Leaderboard\Helpers\Users\LeaderTablesUsers;
use App\Leaderboard\Helpers\Filters\LeaderFilter;

class MainLeaderboard
{
    private int|string $year;
    private string $campaign;
    private ?string $userGroup;
    private int|string|null $month;

    public function __construct(
        int|string $year = 'All',
        string $campaign = 'All',
        ?string $userGroup = 'all',
        int|string|null $month = null
    ) {
        $this->year = $year;
        $this->campaign = $campaign;
        $this->userGroup = $userGroup;
        $this->month = $month;
    }

    public static function createNumbersTable(
        int|string $c_user,
        int|string $c_articles,
        int|string $c_words,
        int|string $c_langs,
        int|string $c_views
    ): string {
        return <<<HTML
        <table class='table compact table-striped'>
            <thead>
                <tr>
                    <th class="spannowrap">Type</th>
                    <th>Number</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><b>Users</b></td><td>$c_user</td></tr>
                <tr><td><b>Articles</b></td><td>$c_articles</td></tr>
                <tr><td><b>Words</b></td><td>$c_words</td></tr>
                <tr><td><b>Languages</b></td><td>$c_langs</td></tr>
                <tr><td><b>Pageviews</b></td><td>$c_views</td></tr>
            </tbody>
        </table>
        HTML;
    }
    public function printCatTable(
        array $graphData,
        string $category,
        array $langs_data,
        bool $addcat
    ): string {
        $leaderboardService = LeaderboardTable::getInstance();

        // Fetch top users and languages using class parameters
        $users = $leaderboardService->getTopUsers(
            $this->year,
            $this->userGroup,
            $category,
            $this->month
        );
        $lang_table = $leaderboardService->getTopLangs(
            $this->year,
            $this->userGroup,
            $category,
            $this->month
        );

        $articles_all = number_format(array_sum(array_column($users, 'count')));

        // Sum all $users[user]["words"] values
        $all_Words = number_format(array_sum(array_column($users, 'words')));
        $all_views = number_format(array_sum(array_column($users, 'views')));

        $numbersTable = self::createNumbersTable(
            count($users),
            $articles_all,
            $all_Words,
            count($lang_table),
            $all_views
        );

        $graph_html = LangUserGraph::graphDataHtmlCard($graphData);

        $numbersCol = Html::makeCol('Numbers', $numbersTable, $graph_html);

        $usersTable = (new LeaderTablesUsers($users))->makeUsersTable();

        $users_list = array_keys($users);
        $users_tab = $leaderboardService->getTopLangOfUsers($users_list);
        $copy_module = LeaderTablesUsers::module_copy_data($users_tab);

        $modal_a = <<<HTML
            <button type="button" class="btn-tool" href="#" data-bs-toggle="modal" data-bs-target="#targets">
                <i class="fas fa-copy"></i>
            </button>
        HTML;

        $usersCol = Html::makeColSm4('Top users by number of translation', $usersTable, 5, $copy_module, $modal_a);

        // Update lang_name in $lang_table
        foreach ($lang_table as $langcode => &$tab) {
            $tab['lang_name'] = $tab['lang_name'] ?? $langs_data[$langcode]['name'] ?? $langcode;
        }
        unset($tab); // Break the reference with the last element

        $languagesTable = (new LeaderTablesLangs($lang_table))->makeLangTable($addcat);

        $languagesCol = Html::makeColSm4('Top languages by number of Articles', $languagesTable, 4);

        return <<<HTML
            <div class="row g-3">
                $numbersCol
                $usersCol
                $languagesCol
            </div>
        HTML;
    }

    public function render(array $langs_data, bool $addcat): string
    {
        $campsToCat = (CategoriesTable::getInstance())->getCampsToCat();
        $category = $campsToCat[$this->campaign] ?? '';

        $leaderboardService = LeaderboardTable::getInstance();

        // Fetch graph data
        $graphData = $leaderboardService->getGraphDataMainLeaderboard(
            $this->year,
            $this->month,
            $category,
            $this->campaign,
            $this->userGroup,
        );

        $months = [];
        if ($this->year && strtolower((string)$this->year) !== 'all') {
            // $months = $graphData['labels'] keys if key start with "{$this->year}-"
            $months = array_filter($graphData['labels'], function ($month): bool {
                return str_starts_with($month, "{$this->year}-");
            });
        }

        $years = (PagesTable::getInstance())->getPagesWithPupdate();
        $categories_tab = (CategoriesTable::getInstance())->getCategories();
        $campaignsList = array_column($categories_tab, 'campaign');

        $projects = (TitlesTable::getInstance())->getProjects();
        $userGroups = array_column($projects, 'g_title');

        $filter_form = LeaderFilter::leaderboardFilter(
            $this->year,
            $this->month,
            $this->userGroup,
            $this->campaign,
            $years,
            $campaignsList,
            $userGroups,
            $months
        );

        $uux = $this->printCatTable(
            $graphData,
            $category,
            $langs_data,
            $addcat
        );

        return <<<HTML
            $filter_form
            <hr/>
            <div class="container-fluid">
                $uux
            </div>
        HTML;
    }
}
