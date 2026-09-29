<?php

namespace App\SQLorAPI\TopData;

use App\SQLorAPI\Leaderboard\LeaderboardTable;

function get_top_lang_of_users($users_original)
{
    return LeaderboardTable::getTopLangOfUsers($users_original);
}

function add_top_params($query, $params, $to_add)
{
    return LeaderboardTable::addTopParams($query, $params, $to_add);
}

function top_query($select)
{
    return LeaderboardTable::topQuery($select);
}

function get_top_users($year, $user_group, $cat, $month = null)
{
    return LeaderboardTable::getTopUsers($year, $user_group, $cat, $month);
}

function get_top_langs($year, $user_group, $cat, $month = null): array
{
    return LeaderboardTable::getTopLangs($year, $user_group, $cat, $month);
}

function get_status($year, $user_group, $cat): array
{
    return LeaderboardTable::getStatus($year, $user_group, $cat);
}
