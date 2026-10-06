<?php

namespace App\Leaderboard\Helpers\Langs;

use App\SQLorAPI\PagesTable;
use App\SQLorAPI\InProcessTable;
use App\Leaderboard\Helpers\Filters\LeadHelp;

class LangsSub
{
    private function add_inp(array $dd_Pending, array $to_add): array
    {

        foreach ($to_add as $_ => $Taab) {
            $kry = LeadHelp::make_key($Taab);

            if (!in_array($kry, array_keys($dd_Pending))) {
                $dd_Pending[$kry] = $Taab;
            }
        }

        return $dd_Pending;
    }

    private function pages_tables(string $mainlang, int|string $year_y): array
    {
        $dd = [];
        $dd_Pending = [];

        $sql_result = (PagesTable::getInstance())->getLangPages($mainlang, $year_y);

        foreach ($sql_result as $yhu => $tabb) {
            if (empty($tabb["lang"] ?? '')) {
                error_log("Missing 'lang' field in entry: " . $yhu);
                continue;
            }

            $kry = LeadHelp::make_key($tabb);

            if (!empty($tabb['target'] ?? '')) {
                $dd[$kry] = $tabb;
            } else {
                $dd_Pending[$kry] = $tabb;
            }
        }

        return ['dd' => $dd, 'dd_Pending' => $dd_Pending];
    }

    public function getTables(string $mainlang, int|string $year_y): array
    {
        $result = [
            'dd' => [],
            'dd_Pending' => [],
            'table_of_views' => []
        ];

        if (empty($mainlang)) {
            return $result;
        }

        $p_tables = $this->pages_tables($mainlang, $year_y);

        $dd = $p_tables['dd'];
        $dd_Pending = $p_tables['dd_Pending'];

        $to_add = (InProcessTable::getInstance())->getLangInProcessByYear($mainlang, (string)$year_y);

        $dd_Pending = $this->add_inp($dd_Pending, $to_add);

        $table_of_views = []; //get_lang_views($mainlang, $year_y);

        $result['dd'] = $dd;
        $result['dd_Pending'] = $dd_Pending;
        $result['table_of_views'] = $table_of_views;

        return $result;
    }
}
