<?php

namespace App\SQLorAPI\GetLead;

use App\SQLorAPI\Leaderboard\LeaderboardTable;

function makeSqlQuery($year, $user_group, $cat)
{
    return LeaderboardTable::makeSqlQuery($year, $user_group, $cat);
}

function makeApiParams($year, $user_group, $cat)
{
    return LeaderboardTable::makeApiParams($year, $user_group, $cat);
}

# @deprecated
function get_leaderboard_table($year, $user_group, $cat)
{
    return LeaderboardTable::getLeaderboardTable($year, $user_group, $cat);
}
