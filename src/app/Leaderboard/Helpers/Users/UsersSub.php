<?php

namespace App\Leaderboard\Helpers\Users;

use App\SQLorAPI\PagesTable;
use App\SQLorAPI\InProcessTable;
use App\Leaderboard\Helpers\Filters\LeadHelp;

class UsersSub
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

    private function pages_tables(string $user_main, int|string $year_y, string $lang_y): array
    {
        $dd = [];
        $dd_Pending = [];

        $sql_result = (PagesTable::getInstance())->getUserPages($user_main, $year_y, $lang_y);

        foreach ($sql_result as $yhu => $tabb) {
            $kry = LeadHelp::make_key($tabb);

            if (!empty($tabb['target'] ?? '')) {
                $dd[$kry] = $tabb;
            } else {
                $dd_Pending[$kry] = $tabb;
            }
        }

        return ['dd' => $dd, 'dd_Pending' => $dd_Pending];
    }

    public function getTables(string $mainuser, int|string $year_y, string $lang_y): array
    {
        $result = [
            'dd' => [],
            'dd_Pending' => [],
            'table_of_views' => []
        ];

        if (empty($mainuser)) {
            return $result;
        }

        $user_main = $mainuser;
        $user_main = rawurldecode(str_replace("_", " ", $user_main));

        $p_tables = $this->pages_tables($user_main, $year_y, $lang_y);

        $dd = $p_tables['dd'];
        $dd_Pending = $p_tables['dd_Pending'];

        $to_add = (InProcessTable::getInstance())->getUserProcessNew($user_main, (string)$year_y);

        $dd_Pending = $this->add_inp($dd_Pending, $to_add);

        krsort($dd);

        krsort($dd_Pending);

        $table_of_views = [];

        $result['dd'] = $dd;
        $result['dd_Pending'] = $dd_Pending;
        $result['table_of_views'] = $table_of_views;

        return $result;
    }
}
