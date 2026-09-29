<?php
// src/leaderboard.php

use App\Layout\PageRunner;
use App\Leaderboard\LeaderboardController;

require_once __DIR__ . '/bootstrap.php';

PageRunner::run(LeaderboardController::class);
