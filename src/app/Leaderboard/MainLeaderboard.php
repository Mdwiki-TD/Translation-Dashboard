<?php

namespace App\Leaderboard;

use App\SQLorAPI\LeaderboardTable;
use App\SQLorAPI\CategoriesTable;
use App\Utils\Html;
use App\Leaderboard\Helpers\Graph\Graph;
use App\Leaderboard\Helpers\Langs\LeaderTablesLangs;
use App\Leaderboard\Helpers\Users\LeaderTablesUsers;
use App\Leaderboard\Helpers\Filters\LeaderFilter;

class MainLeaderboard
{
    private int|string $year;
    private string $camp;
    private ?string $userGroup;
    private int|string|null $month;

    public function __construct(
        int|string $year = 'All',
        string $camp = 'All',
        ?string $userGroup = 'all',
        int|string|null $month = null
    ) {
        $this->year = $year;
        $this->camp = $camp;
        $this->userGroup = $userGroup;
        $this->month = $month;
    }

    public function printCatTable(
        string $cat,
        array $langs_data,
        bool $addcat
    ): string {
        // Fetch top users and languages using class parameters
        $users = (LeaderboardTable::getInstance())->getTopUsers($this->year, $this->userGroup, $cat, $this->month);
        $lang_table = (LeaderboardTable::getInstance())->getTopLangs($this->year, $this->userGroup, $cat, $this->month);

        $articles_all = number_format(array_sum(array_column($users, 'count')));

        // Sum all $users[user]["words"] values
        $all_Words = number_format(array_sum(array_column($users, 'words')));
        $all_views = number_format(array_sum(array_column($users, 'views')));

        $numbersTable = LeaderTablesLangs::createNumbersTable(
            count($users),
            $articles_all,
            $all_Words,
            count($lang_table),
            $all_views
        );

        $graph_data = (LeaderboardTable::getInstance())->getStatus($this->year, $this->userGroup, $cat);
        $graph_html = Graph::print_graph_for_table($graph_data, $no_card = false);

        $numbersCol = Html::makeCol('Numbers', $numbersTable, $graph_html);

        $usersTable = LeaderTablesUsers::makeUsersTable($users);
        $users_list = array_keys($users);

        $users_tab = (LeaderboardTable::getInstance())->getTopLangOfUsers($users_list);
        $copy_module = LeaderTablesUsers::module_copy_data($users_tab);

        $modal_a = <<<HTML
            <button type="button" class="btn-tool" href="#" data-bs-toggle="modal" data-bs-target="#targets">
                <i class="fas fa-copy"></i>
            </button>
        HTML;

        $usersCol = Html::makeColSm4('Top users by number of translation', $usersTable, 5, $copy_module, $modal_a);

        $languagesTable = LeaderTablesLangs::makeLangTable($lang_table, $langs_data, $addcat);
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
        $s_camp_to_cat = (CategoriesTable::getInstance())->getCampsToCat();
        $cat = $s_camp_to_cat[$this->camp] ?? '';

        $filter_form = LeaderFilter::leaderboard_filter($this->year, $this->month, $this->userGroup, $this->camp);

        $uux = $this->printCatTable($cat, $langs_data, $addcat);

        return <<<HTML
            $filter_form
            <hr/>
            <div class="container-fluid">
                $uux
            </div>
        HTML;
    }
}
