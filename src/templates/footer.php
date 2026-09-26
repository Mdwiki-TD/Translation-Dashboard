<?php

use App\Templates\PageFooter;
use App\User\CurrentUser;

include_once __DIR__ . '/templates/PageFooter.php';

$currentUser = CurrentUser::getInstance();

// $timeStart = $pageHeader->getLoadStartTime();
$timeStart = null;

$pageFooter = new PageFooter($currentUser);
$pageFooter->render($timeStart);
