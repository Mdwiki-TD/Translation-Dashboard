<?php

namespace App\Leaderboard\Helpers\Langs;

use App\SQLorAPI\PagesTable;
use App\SQLorAPI\InProcessTable;
use App\Leaderboard\Helpers\Filters\LeadHelp;

class LangsSub
{
    private string $langcode;
    private string|int $year;

    public function __construct(
        string $langcode,
        int|string $year
    ) {
        // Sanitize and format the language code input
        $this->langcode = rawurldecode(str_replace("_", " ", $langcode));

        $this->year = $year;
    }

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

    private function pages_tables(): array
    {
        $dd = [];
        $dd_Pending = [];

        $sql_result = (PagesTable::getInstance())->getLangPages($this->langcode, $this->year);

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

    public function getTables(): array
    {
        $result = [
            'dd' => [],
            'dd_Pending' => [],
            'table_of_views' => []
        ];

        if (empty($this->langcode)) {
            return $result;
        }

        $p_tables = $this->pages_tables();

        $dd = $p_tables['dd'];
        $dd_Pending = $p_tables['dd_Pending'];

        $to_add = (InProcessTable::getInstance())->getLangInProcessByYear($this->langcode, (string)$this->year);

        $dd_Pending = $this->add_inp($dd_Pending, $to_add);

        krsort($dd);
        krsort($dd_Pending);

        $table_of_views = []; //get_lang_views($this->langcode, $this->year);

        $result['dd'] = $dd;
        $result['dd_Pending'] = $dd_Pending;
        $result['table_of_views'] = $table_of_views;

        return $result;
    }
}
