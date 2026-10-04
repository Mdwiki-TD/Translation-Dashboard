<?php
// src/Layout/PageRunner.php

namespace App\Layout;

use App\User\CurrentUser;
use InvalidArgumentException;

final class PageRunner
{
    /**
     * @param class-string $controllerClass
     */
    public static function run(string $controllerClass): void
    {
        if (!class_exists($controllerClass)) {
            throw new InvalidArgumentException("Controller not found: {$controllerClass}");
        }

        $hideNav = isset($_GET['nonav']);

        $currentUser = CurrentUser::getInstance();

        $pageHeader = new PageHeader($currentUser);
        $pageHeader->render($hideNav);

        // Instantiate and execute application router
        (new $controllerClass())->handleRequest();

        $timeStart = $pageHeader->getLoadStartTime();

        $pageFooter = new PageFooter($currentUser);
        $pageFooter->render($timeStart);
    }
}
