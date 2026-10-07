<?php

namespace App\Leaderboard;

use App\SQLorAPI\LeaderboardTable;
use App\SQLorAPI\UsersLeaderboardTable;
use App\Utils\HtmlUrls;
use App\Leaderboard\Helpers\Filters\LeadHelp;
use App\Leaderboard\Helpers\Filters\FilterForm;
use App\Leaderboard\Helpers\Users\UsersSub;
use App\Leaderboard\Helpers\Graph\LangUserGraph;

class UsersLeaderboard
{
    private string $langcode;
    private string $username;
    private string $userToHtml;
    private string $year;
    private string $campaign;

    public function __construct(
        string $username,
        string $langcode,
        int|string $year,
        string $campaign
    ) {
        // Sanitize and format the language code input
        $this->langcode = rawurldecode(str_replace("_", " ", $langcode));
        $this->year = $year;
        $this->campaign = $campaign;

        $this->username = $username;
        // Prepare username for safe rendering in HTML and links
        $this->userToHtml = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    }

    public function render(
        string $global_username,
        array $lead_words_table,
        array $cats_data
    ): string {
        $output = '';

        $usersTable = UsersLeaderboardTable::getInstance();
        $gData = $usersTable->getUserNewFilterData($this->username);

        // "langs": { "ar": 14, "nr": 1, "mg": 2 }
        // TODO: use $gData["langs"] key with max value
        $langs = $gData["langs"] ?? [];
        $userLang = !empty($langs) ? array_search(max($langs), $langs) : '';

        // Fetch user's primary languages
        // '[{"user":"Mr. Ibrahem","lang":"ar","cnt":14}]'
        // $user_most_langs = (LeaderboardTable::getInstance())->getTopLangOfUsers([$this->username]);
        // $userLang = $user_most_langs[0]['lang'] ?? "";

        // Fetch user specific tables
        $service = new UsersSub($this->username, $this->year, $this->langcode);

        $u_tables = $service->getTables($lead_words_table, $cats_data);

        $missingItems = $u_tables['missingItems'];
        $pendingItems = $u_tables['pendingItems'];

        $user_is_global_username = ($global_username === $this->username);

        [$table1, $main_table] = LeadHelp::make_users_lead(
            $missingItems,
            'translations',
            $user_is_global_username
        );

        $user_link = ($userLang)
            ? HtmlUrls::make_wikipedia_url_blank("User:{$this->username}", $userLang, $this->userToHtml)
            : HtmlUrls::make_mdwiki_user_url($this->userToHtml);

        $xtools = HtmlUrls::XtoolsLink($this->userToHtml);

        // Create div for leaderboard header
        $item_div = "<span class='h4 text-center'>User: {$user_link}<br>{$xtools}</span>";

        $filterData = [
            "user" => $this->username,
            "lang" => $this->langcode,
            "year" => $this->year,
            "camp" => $this->campaign
        ];

        // Fetch graph data for the specific user
        $graphData = (LeaderboardTable::getInstance())->getGraphData(
            $this->langcode,
            $this->username,
            $this->year
        );
        $graph = LangUserGraph::graphDataHtml($graphData);

        $output .= FilterForm::leadRow(
            $table1,
            $graph,
            $item_div,
            $filterData,
            $gData,
            "user"
        );

        $output .= <<<HTML
            <div class='card mt-1'>
                <div class='card-body p-1'>
                    $main_table
                </div>
            </div>
        HTML;

        [$_, $table_pnd] = LeadHelp::make_users_lead(
            $pendingItems,
            'pending',
            $user_is_global_username
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
