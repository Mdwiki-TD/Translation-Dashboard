<?php
// src/index.php

use App\Templates\PageHeader;
use App\Templates\PageFooter;

use App\User\CurrentUser;
use App\AppRouter;

include_once __DIR__ . '/app/include_all.php';
include_once __DIR__ . '/app/index.php'; // AppRouter
include_once __DIR__ . '/templates/include.php';

$currentUser = CurrentUser::getInstance();

$pageHeader = new PageHeader($currentUser);
$pageHeader->render();

// Instantiate and execute application router
$router = new AppRouter($currentUser);
$router->handleRequest();

$timeStart = $pageHeader->getLoadStartTime();

$pageFooter = new PageFooter($currentUser);
$pageFooter->render($timeStart);
