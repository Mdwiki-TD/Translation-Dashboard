<?php

namespace App\Leaderboard;

use App\Tables\LangsTables;
use App\SQLorAPI\ViewsTable;
use App\Leaderboard\Helpers\Filters\LeadHelp;
use App\Leaderboard\Helpers\Langs\LangsSub;
use App\Leaderboard\Helpers\Graph\LangUserGraph;
use App\Leaderboard\Helpers\Filters\FilterForm;

class LangsLeaderboard
{
    private string $langcode;

    public function __construct(string $langcode)
    {
        // Sanitize and format the language code input
        $this->langcode = rawurldecode(str_replace("_", " ", $langcode));
    }

    public function render(
        int|string $year_y,
        string $camp,
        array $lead_words_table,
        array $cats_data
    ): string {
        $output = '';

        // Fetch display language name or fallback to code
        $langname = LangsTables::get_lang_name($this->langcode) ?? $this->langcode;

        // Fetch language specific data tables
        $u_tables = (new LangsSub($this->langcode, $year_y))->getTables();

        $dd = $u_tables['dd'];
        $dd_Pending = $u_tables['dd_Pending'];
        $table_of_views = $u_tables['table_of_views'];

        [$table1, $main_table] = LeadHelp::make_langs_lead(
            $dd,
            'translations',
            $table_of_views,
            $lead_words_table,
            $cats_data
        );

        // Create div for leaderboard header
        $item_div = "<h4 class='text-center'>Language: $langname ({$this->langcode})</h4>";

        $filter_data = [
            "user" => "",
            "lang" => $this->langcode,
            "year" => $year_y,
            "camp" => $camp
        ];

        // Fetch graph data for the specific language
        $graphData = (ViewsTable::getInstance())->getGraphData(
            $this->langcode,
            null,
            $year_y
        );
        $graph = LangUserGraph::graphData($graphData);

        $output .= FilterForm::lead_row(
            $table1,
            $graph,
            $item_div,
            $filter_data,
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
            $dd_Pending,
            'pending',
            $table_of_views,
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
