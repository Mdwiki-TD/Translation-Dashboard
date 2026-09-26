<?php

use App\Templates\PageHeader;
use App\User\CurrentUser;

include_once __DIR__ . '/PageHead.php';
include_once __DIR__ . '/PageHeader.php';

$currentUser = CurrentUser::getInstance();

$pageHeader = new PageHeader($currentUser);
$pageHeader->render();
