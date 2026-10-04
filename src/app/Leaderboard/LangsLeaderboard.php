<?php

namespace App\Leaderboard;

use App\Leaderboard\Helpers\Filters\LeadHelp;
use App\Leaderboard\Helpers\Langs\LangsSub;
use App\Leaderboard\Helpers\Graph\LangUserGraph;
use App\Leaderboard\Helpers\Filters\FilterForm;
use App\Tables\LangsTables;

class LangsLeaderboard
{
    public function render(
        $mainlang,
        $year_y,
        $camp,
        $lead_words_table,
        $cats_data,
    ): string {
        $output = '';

        $mainlang = rawurldecode(str_replace("_", " ", $mainlang));

        $langname = LangsTables::get_lang_name($mainlang) ?? $mainlang;

        $u_tables = LangsSub::get_langs_tables($mainlang, $year_y);

        $dd = $u_tables['dd'];
        $dd_Pending = $u_tables['dd_Pending'];
        $table_of_views = $u_tables['table_of_views'];

        [$table1, $main_table] = LeadHelp::make_langs_lead(
            $dd,
            'translations',
            $table_of_views,
            $mainlang,
            $lead_words_table,
            $cats_data,
        );

        $graph = LangUserGraph::graph_data_new($dd);

        $filter_data = ["user" => "", "lang" => $mainlang, "year" => $year_y, "camp" => $camp];

        $output .= FilterForm::lead_row($table1, $graph, "<h4 class='text-center'>Language: $langname ($mainlang)</h4>", $filter_data, "lang");

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
            $mainlang,
            $lead_words_table,
            $cats_data,
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
