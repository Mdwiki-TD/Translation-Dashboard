<?php
// src/leaderboard_js.php

use App\Layout\PageRunner;
use App\Controllers\LeaderboardJsController;

require_once __DIR__ . '/bootstrap.php';

PageRunner::run(LeaderboardJsController::class);
