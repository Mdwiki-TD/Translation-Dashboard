<?php
// src/index.php

use App\Layout\PageRunner;
use App\AppRouter;

require_once __DIR__ . '/bootstrap.php';

PageRunner::run(AppRouter::class);
