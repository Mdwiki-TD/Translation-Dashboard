<?php
// src/missing.php

use App\Layout\PageRunner;
use App\Missing\MissingController;

require_once __DIR__ . '/bootstrap.php';

PageRunner::run(MissingController::class);
