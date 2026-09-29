<?php
// src/sitelinks.php

use App\Layout\PageRunner;
use App\SiteLinks\SiteLinksController;

require_once __DIR__ . '/bootstrap.php';

PageRunner::run(SiteLinksController::class);
