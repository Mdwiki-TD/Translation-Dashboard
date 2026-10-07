<?php

namespace App\Leaderboard\Helpers\Filters;

use App\Utils\Html;

class LeaderFilter
{
    private int|string $year;
    private int|string|null $month;
    private ?string $userGroup;
    private string $campaign;

    public function __construct(
        int|string $year,
        int|string|null $month,
        ?string $userGroup,
        string $campaign,
    ) {
        $this->year = $year;
        $this->month = $month;
        $this->userGroup = $userGroup;
        $this->campaign = $campaign;
    }

    private function FilterForm(
        string $action,
        string $campDropdown,
        string $projectDropdown,
        string $yearDropdown,
        string $monthDropdown
    ): string {
        return <<<HTML
            <form method="GET" action="$action" id="leaderboard_filter">
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
    private function inputGroup(string $title, string $rows): string
    {
        $d33 = <<<HTML
            <div class="input-group">
                <span class="input-group-text">%s</span>
                %s
            </div>
        HTML;

        return sprintf($d33, $title, $rows);
    }

    private function makeCampDropdown(string $campaign, array $campaignsList): string
    {

        $y1 = Html::makeDropdown($campaignsList, $campaign, 'camp', 'all');

        return $this->inputGroup('Campaign', $y1);
    }

    private function makeUserGroupDropdown(?string $userGroup, array $userGroups): string
    {

        $y2 = Html::makeDropdown($userGroups, (string)$userGroup, 'user_group', 'all');

        return $this->inputGroup('Translators', $y2);
    }

    private function makeYearDropdown(int|string $year, array $years): string
    {
        // sort $m_years2 DESC
        rsort($years);

        $y3 = Html::makeDropdown($years, (string)$year, 'year', 'all');

        return $this->inputGroup('Year', $y3);
    }

    private function makeMonthDropdown(int|string|null $month, array $months): string
    {

        // array ( '2024-01' => 92, '2024-02' => 222, '2024-03' => 231, '2024-04' => 160, '2024-05' => 214, '2024-06' => 146, '2024-07' => 145, '2024-08' => 73, '2024-09' => 503, '2024-10' => 359, '2024-11' => 207, '2024-12' => 204, )

        // 2024-01 > 01

        $months_list = $months;
        $months_list = array_unique(array_map(
            fn($item) => date(
                'm',
                strtotime($item)
            ),
            $months
        ));

        // sort $m_months ASC
        asort($months_list);

        $y3 = Html::makeDropdown($months_list, (string)$month, 'month', 'All');

        // $monthDropdown = inputGroup('Month', $y3);
        return <<<HTML
            <div class="input-group w-50">
                $y3
            </div>
        HTML;
    }

    public function leaderboardFilter(
        array $years,
        array $campaignsList,
        array $userGroups,

        ?array $months = null,
        string $action = "leaderboard.php"
    ): string {
        $campDropdown = $this->makeCampDropdown($this->campaign, $campaignsList);
        $projectDropdown = $this->makeUserGroupDropdown($this->userGroup, $userGroups);
        $yearDropdown = $this->makeYearDropdown($this->year, $years);

        $monthDropdown = "";
        if (is_array($months) && !empty($months)) {
            $monthDropdown = $this->makeMonthDropdown($this->month, $months);
        }

        return $this->FilterForm(
            $action,
            $campDropdown,
            $projectDropdown,
            $yearDropdown,
            $monthDropdown
        );
    }
}
