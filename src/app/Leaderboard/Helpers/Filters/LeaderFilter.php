<?php

namespace App\Leaderboard\Helpers\Filters;

use App\SQLorAPI\LeaderboardTable;
use App\SQLorAPI\TitlesTable;
use App\SQLorAPI\CategoriesTable;
use App\SQLorAPI\PagesTable;
use App\Utils\Html;

class LeaderFilter
{
    public static function input_group(string $title, string $rows): string
    {
        $d33 = <<<HTML
            <div class="input-group">
                <span class="input-group-text">%s</span>
                %s
            </div>
        HTML;

        return sprintf($d33, $title, $rows);
    }

    public static function make_camp_dropdown(string $campaign): string
    {
        $categories_tab = (CategoriesTable::getInstance())->getCategories();
        $categories_tab = array_column($categories_tab, 'campaign');

        $y1 = Html::makeDropdown($categories_tab, $campaign, 'camp', 'all');

        return self::input_group('Campaign', $y1);
    }

    public static function make_project_dropdown(?string $user_group): string
    {
        $projects_tab = (TitlesTable::getInstance())->getProjects();

        $user_groups = array_column($projects_tab, 'g_title');

        // '["Benevity","Hearing","McMaster","OLI","ProZ","Shani","TWB","TWB\\/WikiMed (Arabic)","Uncategorized","Wiki"]'
        // var_export(json_encode($user_groups));

        $y2 = Html::makeDropdown($user_groups, (string)$user_group, 'user_group', 'all');

        return self::input_group('Translators', $y2);
    }

    public static function make_year_dropdown(int|string $year): string
    {
        $m_years2 = (PagesTable::getInstance())->getPagesWithPupdate();


        // sort $m_years2 from biggest to smallest
        rsort($m_years2);

        $y3 = Html::makeDropdown($m_years2, (string)$year, 'year', 'all');

        return self::input_group('Year', $y3);
    }

    public static function make_month_dropdown(int|string|null $month, array $months): string
    {

        // array ( '2024-01' => 92, '2024-02' => 222, '2024-03' => 231, '2024-04' => 160, '2024-05' => 214, '2024-06' => 146, '2024-07' => 145, '2024-08' => 73, '2024-09' => 503, '2024-10' => 359, '2024-11' => 207, '2024-12' => 204, )

        // 2024-01 > 01

        $months_list = array_unique(array_map(
            fn($item) => date(
                'm',
                strtotime($item)
            ),
            $months
        ));

        // sort $m_months from biggest to smallest
        rsort($months_list);

        $y3 = Html::makeDropdown($months_list, (string)$month, 'month', 'All');

        // $monthDropdown = input_group('Month', $y3);
        return <<<HTML
            <div class="input-group w-50">
                $y3
            </div>
        HTML;
    }

    public static function leaderboard_filter(
        int|string $year,
        int|string|null $month,
        ?string $user_group,
        string $campaign,
        string $action = "leaderboard.php"
    ): string {
        $campDropdown = self::make_camp_dropdown($campaign);

        $projectDropdown = self::make_project_dropdown($user_group);

        $yearDropdown = self::make_year_dropdown($year);

        $s_camp_to_cat = (CategoriesTable::getInstance())->getCampsToCat();
        $cat = $s_camp_to_cat[$campaign] ?? '';

        $monthDropdown = "";

        if ((string)$year !== 'all') {

            $graph_data = (LeaderboardTable::getInstance())->getStatus($year, $user_group, $cat);
            $months = array_keys($graph_data);

            $monthDropdown = self::make_month_dropdown($month, $months);
        }

        return <<<HTML
            <form method="get" action="$action" id="leaderboard_filter">
                <div class="row g-3">
                    <div class="col-md-3">
                        <span align="center">
                            <h3>Leaderboard</h3>
                        </span>
                    </div>
                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-md-4">
                                $campDropdown
                            </div>
                            <div class="col-md-4">
                                $projectDropdown
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    $yearDropdown
                                    $monthDropdown
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="aligncenter col-md-1 col-sm-3">
                        <input class='btn btn-outline-primary' type='submit' value='Filter' />
                    </div>
                </div>
            </form>
        HTML;
    }
}
