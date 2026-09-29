<?php

include_once __DIR__ . '/leader_tables.php';
include_once __DIR__ . '/leader_tables_users.php';
include_once __DIR__ . '/leader_filter.php';

# subs
include_once __DIR__ . '/subs/filter_form.php';
include_once __DIR__ . '/subs/langs_sub.php';
include_once __DIR__ . '/subs/lead_help.php';
include_once __DIR__ . '/subs/users_sub.php';

# namespace App\Leaderboard
include_once __DIR__ . '/LeaderboardRouter.php';
include_once __DIR__ . '/MainLeaderboard.php';
include_once __DIR__ . '/UsersLeaderboard.php';
include_once __DIR__ . '/LangsLeaderboard.php';

include_once __DIR__ . '/graph.php';
include_once __DIR__ . '/lang_user_graph.php';
include_once __DIR__ . '/camps.php';

# others
include_once __DIR__ . '/others/camps_text.php';
include_once __DIR__ . '/others/graph_api.php';
