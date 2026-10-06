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
    private string $username;
    private string $userToHtml;

    public function __construct(string $username)
    {
        $this->username = $username;
        // Prepare username for safe rendering in HTML and links
        $this->userToHtml = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    }
    public function render(
        string $mainlang,
        int|string $year_y,
        string $camp,
        string $global_username,
        array $lead_words_table,
        array $cats_data
    ): string {
        $output = '';

        $mainlang = rawurldecode(str_replace("_", " ", $mainlang));

        // Fetch user's primary languages
        // '[{"user":"Mr. Ibrahem","lang":"ar","cnt":14}]'
        $user_most_langs = (LeaderboardTable::getInstance())->getTopLangOfUsers([$this->username]);
        $user_langs = $user_most_langs[0]['lang'] ?? "";

        // Fetch user specific tables
        $u_tables = UsersSub::get_users_tables($this->username, $year_y, $mainlang);

        $dd = $u_tables['dd'];
        $dd_Pending = $u_tables['dd_Pending'];
        $table_of_views = $u_tables['table_of_views'];

        $user_is_global_username = ($global_username === $this->username);

        [$table1, $main_table] = LeadHelp::make_users_lead(
            $dd,
            'translations',
            $table_of_views,
            $user_is_global_username,
            $lead_words_table,
            $cats_data
        );

        $user_link = ($user_langs)
            ? HtmlUrls::make_wikipedia_url_blank("User:{$this->username}", $user_langs, $this->userToHtml)
            : HtmlUrls::make_mdwiki_user_url($this->userToHtml);

        $xtools = <<<HTML
            <a href='https://xtools.wmflabs.org/globalcontribs/{$this->userToHtml}' target='_blank'>
                <img class="splash-logo" src="/Translation_Dashboard/static/xtools.svg" alt="XTools" width="80" height="35" title="Xtools">
            </a>
        HTML;

        $user_div = <<<HTML
            <span class='h4 text-center'>
                User: $user_link
                <br>
                $xtools
            </span>
        HTML;

        $filter_data = [
            "user" => $this->username,
            "lang" => $mainlang,
            "year" => $year_y,
            "camp" => $camp
        ];

        $graphData = (ViewsTable::getInstance())->getGraphData($mainlang, $this->username, $year_y);
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
