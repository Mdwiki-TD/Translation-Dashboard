<?php

namespace App\Leaderboard\Helpers\Users;

use App\SQLorAPI\PagesTable;
use App\SQLorAPI\InProcessTable;
use App\Leaderboard\Helpers\Filters\LeadHelp;

class UsersSub
{
    public static function add_inp(array $dd_Pending, string $user, int|string $year_y): array
    {
        $to_add = (InProcessTable::getInstance())->getUserProcessNew($user, (string)$year_y);

        foreach ($to_add as $_ => $Taab) {
            $kry = LeadHelp::make_key($Taab);

            if (!in_array($kry, array_keys($dd_Pending))) {
                $dd_Pending[$kry] = $Taab;
            }
        }

        return $dd_Pending;
    }

    public static function pages_tables(string $user_main, int|string $year_y, string $lang_y): array
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

    public static function get_users_tables(string $mainuser, int|string $year_y, string $lang_y): array
    {
        $result = ['dd' => [], 'dd_Pending' => [], 'table_of_views' => []];

        if (empty($mainuser)) {
            return $result;
        }

        $user_main = $mainuser;
        $user_main = rawurldecode(str_replace("_", " ", $user_main));

        $p_tables = self::pages_tables($user_main, $year_y, $lang_y);

        $dd = $p_tables['dd'];
        $dd_Pending = $p_tables['dd_Pending'];

        $dd_Pending = self::add_inp($dd_Pending, $user_main, $year_y);

        krsort($dd);

        krsort($dd_Pending);

        $table_of_views = [];

        $result['dd'] = $dd;
        $result['dd_Pending'] = $dd_Pending;
        $result['table_of_views'] = $table_of_views;

        return $result;
    }
}
