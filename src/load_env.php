<?php

# Prevent loading .env files in production environment
if (getenv('APP_ENV') === 'production') {
    // In production, we should not load .env files
    error_log('Attempted to load .env file in production environment');
    return;
}

if (getenv('APP_ENV') === 'testing') {
    // In test environment, we can load .env files but with strict checks
    error_log('Loading .env file in test environment');
}

if (!function_exists('str_ends_with')) {
    function str_ends_with($string, $endString)
    {
        $len = strlen($endString);
        return $len === 0 || substr($string, -$len) === $endString;
    }
}

if (!function_exists('str_starts_with')) {
    function str_starts_with($text, $start)
    {
        return strpos($text, $start) === 0;
    }
}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

/**
 * Load .env file securely with whitelist and permission checks.
 *
 * @param string $filePath
 * @param array $allowedKeys
 * @return void
 * @throws RuntimeException
 */
function loadEnvironmentVariables(string $filePath, array $allowedKeys = []): void
{
    if (!file_exists($filePath) || !is_readable($filePath)) {
        echo "Warning: Env file not found or not readable: $filePath\n";
        throw new RuntimeException("Env file not found or not readable: $filePath");
    }
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Skip empty lines and comments
        if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, ';')) {
            continue;
        }

        // Must contain '='
        if (!str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Only allow keys in whitelist if defined
        if (!empty($allowedKeys) && !in_array($key, $allowedKeys, true)) {
            continue;
        }

        // Remove surrounding quotes
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        // Prevent overwriting existing environment variables
        if (array_key_exists($key, $_ENV) || getenv($key) !== false) {
            continue;
        }

        $_ENV[$key] = $value;

        // Expose to getenv()
        if (!@putenv($key . '=' . $value)) {
            throw new RuntimeException("Failed to set environment variable: $key");
        }
    }
}

try {
    $envFile = dirname(__DIR__) . '/.env';

    // Define whitelist of allowed keys
    $whitelist = [
        'DB_HOST',
        'TOOL_TOOLSDB_USER',
        'TOOL_TOOLSDB_PASSWORD',
        'DB_NAME',
        'DB_NAME_NEW',
        'COOKIE_KEY',
    ];

    loadEnvironmentVariables($envFile, $whitelist);
} catch (RuntimeException $e) {
    error_log('ENV Loader Error: ' . $e->getMessage());
    exit(1); // Fail-fast
}
