<?php

namespace App;

class Logger
{
    private static ?bool $debug = null;

    /**
     * Check and cache the debug status.
     *
     * @return bool
     */
    private static function isDebug(): bool
    {
        if (self::$debug === null) {
            if (isset($_COOKIE['test']) && $_COOKIE['test'] === 'x') {
                self::$debug = false;
            } else {
                self::$debug = isset($_REQUEST['test']) || isset($_COOKIE['test']);
            }
        }

        return self::$debug;
    }

    /**
     * Print debug information if the test condition is met.
     *
     * @param mixed $s Data to be logged or printed.
     * @return void
     */
    public static function debug(mixed $s): void
    {
        if (!self::isDebug()) {
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
