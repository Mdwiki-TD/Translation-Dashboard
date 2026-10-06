<?php

namespace App\Leaderboard;

use App\SQLorAPI\LeaderboardTable;
use App\SQLorAPI\ViewsTable;
use App\Utils\HtmlUrls;
use App\Leaderboard\Helpers\Filters\LeadHelp;
use App\Leaderboard\Helpers\Filters\FilterForm;
use App\Leaderboard\Helpers\Users\UsersSub;
use App\Leaderboard\Helpers\Graph\LangUserGraph;

class UsersLeaderboard
{
    public string $username;
    public function __construct(string $username) {
        $this->username = $username;
    }
    public function render(
        string $mainlang,
        int|string $year_y,
        string $camp,
        string $user_to_curl,
        string $user_to_html,
        string $global_username,
        array $lead_words_table,
        array $cats_data,
    ): string {
        $output = '';

        $mainlang = rawurldecode(str_replace("_", " ", $mainlang));

        // '[{"user":"Mr. Ibrahem","lang":"ar","cnt":14}]'
        $user_most_langs = (LeaderboardTable::getInstance())->getTopLangOfUsers([$user_to_curl]);

        $user_langs = $user_most_langs[0]['lang'] ?? "";

        $u_tables = UsersSub::get_users_tables($user_to_curl, $year_y, $mainlang);

        $dd = $u_tables['dd'];
        $dd_Pending = $u_tables['dd_Pending'];
        $table_of_views = $u_tables['table_of_views'];

        $user_is_global_username = ($global_username === $user_to_curl) ? true : false;

        [$table1, $main_table] = LeadHelp::make_users_lead(
            $dd,
            'translations',
            $table_of_views,
            $user_is_global_username,
            $lead_words_table,
            $cats_data
        );

        $user_link = ($user_langs)
            ? HtmlUrls::make_wikipedia_url_blank("User:$user_to_curl", $user_langs, $user_to_html)
            : HtmlUrls::make_mdwiki_user_url($user_to_html);

        $xtools = <<<HTML
            <!-- <div class="d-flex align-items-center justify-content-between"> -->
                <a href='https://xtools.wmflabs.org/globalcontribs/$user_to_html' target='_blank'>
                    <img class="splash-logo" src="/Translation_Dashboard/static/xtools.svg" alt="XTools" width="80" height="35" title="Xtools">
                    <!-- <span class='h4'>(XTools)</span> -->
                </a>
            <!-- </div> -->
        HTML;

        $user_div = <<<HTML
            <span class='h4 text-center'>
                User: $user_link
                <br>
                $xtools
            </span>
        HTML;

        $filter_data = ["user" => $user_to_curl, "lang" => $mainlang, "year" => $year_y, "camp" => $camp];

        $graphData = (ViewsTable::getInstance())->getGraphData($mainlang, $user_to_curl, $year_y);

        $graph = LangUserGraph::graphData($graphData);

        $output .= FilterForm::lead_row($table1, $graph, $user_div, $filter_data, "user");

        $output .= <<<HTML
            <div class='card mt-1'>
                <div class='card-body p-1'>
                    $main_table
                </div>
            </div>
        HTML;

        [$_, $table_pnd] = LeadHelp::make_users_lead(
            $dd_Pending,
            'pending',
            $table_of_views,
            $user_is_global_username,
            $lead_words_table,
            $cats_data
        );

        $output .= <<<HTML
            <br>
            <div class='card'>
                <div class='card-body' style='padding:5px 0px 5px 5px;'>
                    <h2 class='text-center'>Translations in process</h2>
                    $table_pnd
                </div>
            </div>
        HTML;

        return $output;
    }
}
