<?php

namespace App\Leaderboard;

use App\Tables\LangsTables;
use App\SQLorAPI\PagesTable;
use App\SQLorAPI\LeaderboardTable;
use App\Leaderboard\Helpers\Filters\LeadHelp;
use App\Leaderboard\Helpers\Langs\LangsSub;
use App\Leaderboard\Helpers\Graph\LangUserGraph;
use App\Leaderboard\Helpers\Filters\FilterForm;

class LangsLeaderboard
{
    private string $langcode;
    private string $year;
    private string $campaign;

    public function __construct(
        string $langcode,
        int|string $year,
        string $campaign
    ) {
        // Sanitize and format the language code input
        $this->langcode = rawurldecode(str_replace("_", " ", $langcode));
        $this->year = $year;
        $this->campaign = $campaign;
    }

    public function render(
        array $lead_words_table,
        array $cats_data
    ): string {
        $output = '';

        // Fetch display language name or fallback to code
        $langname = LangsTables::get_lang_name($this->langcode) ?? $this->langcode;

        // Fetch language specific data tables
        $service = new LangsSub($this->langcode, $this->year);

        $u_tables = $service->getTables($lead_words_table, $cats_data);

        $missingItems = $u_tables['missingItems'];
        $pendingItems = $u_tables['pendingItems'];

        [$table1, $main_table] = LeadHelp::make_langs_lead(
            $missingItems,
            'translations',
        );

        // Create div for leaderboard header
        $item_div = "<h4 class='text-center'>Language: $langname ({$this->langcode})</h4>";

        $filterData = [
            "user" => "",
            "lang" => $this->langcode,
            "year" => $this->year,
            "camp" => $this->campaign
        ];

        // Fetch graph data for the specific language
        $graphData = (LeaderboardTable::getInstance())->getGraphData(
            $this->langcode,
            null,
            $this->year
        );
        $graph = LangUserGraph::graphDataHtml($graphData);

        $pagesTable = PagesTable::getInstance();
        $gData = ["years" => $pagesTable->getLangYears($this->langcode)];

        $output .= FilterForm::leadRow(
            $table1,
            $graph,
            $item_div,
            $filterData,
            $gData,
            "lang"
        );

        $output .= <<<HTML
            <div class='card mt-1'>
                <div class='card-body p-1'>
                    $main_table
                </div>
            </div>
        HTML;

        [$_, $table_pnd] = LeadHelp::make_langs_lead(
            $pendingItems,
            'pending',
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
