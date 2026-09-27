<?php

namespace App\SQLorAPI\TopData;

use App\SQLorAPI\Leaderboard\LeaderboardTable;

function get_td_or_sql_top_lang_of_users($users_original)
{
    return LeaderboardTable::getTdOrSqlTopLangOfUsers($users_original);
}

function add_top_params($query, $params, $to_add)
{
    return LeaderboardTable::addTopParams($query, $params, $to_add);
}

function top_query($select)
{
    return LeaderboardTable::topQuery($select);
}

function get_td_or_sql_top_users($year, $user_group, $cat, $month = null)
{
    return LeaderboardTable::getTdOrSqlTopUsers($year, $user_group, $cat, $month);
}

function get_td_or_sql_top_langs($year, $user_group, $cat, $month = null): array
{
    return LeaderboardTable::getTdOrSqlTopLangs($year, $user_group, $cat, $month);
}

function get_td_or_sql_status($year, $user_group, $cat): array
{
    return LeaderboardTable::getTdOrSqlStatus($year, $user_group, $cat);
}
