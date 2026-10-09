<?php
// src/app/Logger.php
declare (strict_types = 1);

namespace App;

final class Logger
{
    /** @var (callable(string, string): void)|null */
    private static $sink        = null;
    private static ?bool $debug = null;

    /**
     * Replace the output sink. Pass null to restore the default (error_log).
     *
     * @param (callable(string $level, string $message): void)|null $sink
     */
    public static function setSink( ? callable $sink) : void
    {
        self::$sink = $sink;
    }

    /** Reset cached state (useful in tests). */
    public static function reset(): void
    {
        self::$sink  = null;
        self::$debug = null;
    }

    public static function debug(mixed $message): void
    {
        if (self::isDebug()) {
            self::write('debug', self::stringify($message));
        }
    }

    public static function info(string $message): void
    {
        self::write('info', $message);
    }

    public static function warning(string $message): void
    {
        self::write('warning', $message);
    }

    public static function error(string $message): void
    {
        self::write('error', $message);
    }

    private static function write(string $level, string $message): void
    {
        if (self::$sink !== null) {
            (self::$sink)($level, $message);
            return;
        }

        // In CLI under "testing", stay silent unless a sink is installed.
        if (self::env('APP_ENV') === 'testing') {
            return;
        }

        error_log($level === 'error' ? $message : "[$level] $message");
    }

    private static function isDebug(): bool
    {
        return self::$debug ??= (self::env('APP_ENV') === 'development');
    }

    private static function stringify(mixed $value): string
    {
        return is_string($value) ? $value : print_r($value, true);
    }

    private static function env(string $key): string
    {
        $value = getenv($key);
        return $value !== false && $value !== '' ? $value : (string) ($_ENV[$key] ?? '');
    }
}
