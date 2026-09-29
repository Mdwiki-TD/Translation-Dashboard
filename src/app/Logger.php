<?php

namespace App;

class Logger
{
    private static ?bool $isDebugEnabled = null;

    /**
     * Check and cache the debug status.
     *
     * @return bool
     */
    private static function isDebugEnabled(): bool
    {
        if (self::$isDebugEnabled === null) {
            if (isset($_COOKIE['test']) && $_COOKIE['test'] === 'x') {
                self::$isDebugEnabled = false;
            } else {
                self::$isDebugEnabled = isset($_REQUEST['test']) || isset($_COOKIE['test']);
            }
        }

        return self::$isDebugEnabled;
    }

    /**
     * Print debug information if the test condition is met.
     *
     * @param mixed $s Data to be logged or printed.
     * @return void
     */
    public static function debug(mixed $s): void
    {
        if (!self::isDebugEnabled()) {
            return;
        }

        if (is_string($s)) {
            echo "\n<br>\n$s";
        } else {
            echo "\n<br>\n";
            print_r($s);
        }
    }
}
