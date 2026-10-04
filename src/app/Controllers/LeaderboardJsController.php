<?php

namespace App\Controllers;

use App\Leaderboard\Helpers\Filters\LeaderFilter;

class LeaderboardJsController
{
    public function renderFilterForm(): string
    {
        $year  = strtolower(filter_input(INPUT_GET, 'year', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'all');
        $month = strtolower(filter_input(INPUT_GET, 'month', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $camp  = strtolower(filter_input(INPUT_GET, 'camp', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'all');

        $user_group = filter_input(INPUT_GET, 'project', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?? filter_input(INPUT_GET, 'user_group', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?? 'all';

        $user_group = strtolower($user_group);

        return LeaderFilter::leaderboard_filter($year, $month, $user_group, $camp, 'x.php');
    }

    public function renderNumbersCard(): string
    {
        return <<<HTML
            <div class="card card2 mb-3">
                <div class="card-header">
                    <span class="card-title" style="font-weight:bold;">
                        Numbers
                    </span>
                    <div style='float: right'>
                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body1 card2">
                    <table class='table compact table-striped table_text_left leaderboard_tables'>
                        <thead>
                            <tr>
                                <th class="spannowrap">Type</th>
                                <th>Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Users</b></td>
                                <td><span id="c_user">0</span></td>
                            </tr>
                            <tr>
                                <td><b>Articles</b></td>
                                <td><span id="c_articles">0</span></td>
                            </tr>
                            <tr>
                                <td><b>Words</b></td>
                                <td><span id="c_words">0</span></td>
                            </tr>
                            <tr>
                                <td><b>Languages</b></td>
                                <td><span id="c_lang">0</span></td>
                            </tr>
                            <tr>
                                <td><b>Pageviews</b></td>
                                <td><span id="c_pv">0</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        HTML;
    }

    public function renderTranslationByMonthCard(): string
    {
        return <<<HTML
            <div class="card">
                <div class="card-header aligncenter" style="font-weight:bold;">
                    Translation by month
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card5px">
                    <div class="position-relative">
                        <canvas id="chart09" height="200" class="invert-on-dark"></canvas>
                    </div>
                </div>
            </div>
        HTML;
    }

    public function renderTopUsersCard(): string
    {
        return <<<HTML
            <div class="card card2 mb-3">
                <div class="card-header">
                    <span class="card-title" style="font-weight:bold;">
                        Top users by number of translation
                    </span>
                    <div style='float: right'>
                        <button type="button" class="btn-tool" href="#" data-bs-toggle="modal" data-bs-target="#targets">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card2">
                    <table class='table compact table-striped table_text_left leaderboard_tables' id='Topusers'
                        style='margin-top: 0px !important;margin-bottom: 0px !important'>
                        <thead>
                            <tr>
                                <th class="spannowrap">#</th>
                                <th class="spannowrap">User</th>
                                <th>Number</th>
                                <th>Words</th>
                                <th>Pageviews</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
        HTML;
    }

    public function renderTopLangsCard(): string
    {
        return <<<HTML
            <div class="card card2 mb-3">
                <div class="card-header">
                    <span class="card-title" style="font-weight:bold;">
                        Top languages by number of Articles
                    </span>
                    <div style='float: right'>

                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card2">
                    <table class='table compact table-striped table_text_left leaderboard_tables' id='Toplangs'
                        style='margin-top: 0px !important;margin-bottom: 0px !important'>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class='spannowrap'>Language</th>
                                <th>Count</th>
                                <!-- <th>Words</th> -->
                                <th>Pageviews</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        HTML;
    }
    public function handleRequest(): void
    {
        $filterFormHtml             = $this->renderFilterForm();
        $numbersCardHtml            = $this->renderNumbersCard();
        $translationByMonthCardHtml = $this->renderTranslationByMonthCard();
        $topUsersCardHtml           = $this->renderTopUsersCard();
        $topLangsCardHtml           = $this->renderTopLangsCard();

        echo <<<HTML
            <script src="/Translation_Dashboard/js/graph_js.js"></script>
            <script src="/Translation_Dashboard/js/graph_api.js"></script>
            {$filterFormHtml}
            <hr />
            <div class="container-fluid">
                <div class="row g-3">
                    <div class="col-md-3">
                        {$numbersCardHtml}
                        {$translationByMonthCardHtml}
                    </div>
                    <div class="col-md-5">
                        {$topUsersCardHtml}
                    </div>
                    <div class="col-md-4">
                        {$topLangsCardHtml}
                    </div>
                </div>
            </div>
            <script src="/Translation_Dashboard/js/leaderboard_index_js.js"></script>
            <script>
                // when page ready
                $(document).ready(async function() {
                    await renderJsLeaderboard();
                });
            </script>
            HTML;
    }
}
