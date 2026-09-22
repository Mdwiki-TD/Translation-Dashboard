<?php

/**
 * Application Bootstrap and Include Module
 *
 * This file serves as the central bootstrap for the Translation Dashboard
 * application. It handles environment setup and includes all necessary
 * dependencies in the correct order.
 *
 * Include Order:
 * 1. CSRF protection module
 * 2. Configuration settings
 * 3. Utility functions (glob-loaded)
 * 4. OAuth authentication (environment-specific path)
 * 5. API call modules (glob-loaded)
 * 6. API/SQL abstraction layer (glob-loaded)
 * 7. Database table definitions (glob-loaded, special handling for langcode)
 * 8. Results processing modules
 * 9. Coordinator tools helpers
 *
 * Environment Setup:
 * - Sets HOME environment variable for configuration file discovery
 * - Handles Windows development environment (I:/MD_TOOLS/MDWIKI_MAIN_REPO)
 * - Production uses Toolforge standard HOME path
 *
 * Usage:
 * ```php
 * // Include at the start of any entry point
 * include_once __DIR__ . '/include.php';
 * ```
 *
 * @package    Core
 * @subpackage Bootstrap
 * @author     Translation Dashboard Team
 * @version    2.0.0
 * @since      1.0.0
 * @license    GPL-3.0-or-later
 */

// Enable debug mode via request or cookie
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}


// Configure secure session settings
ini_set('session.use_strict_mode', '1');

// don't use OAuth\Settings\Settings here, Instance is not created yet
$env = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'development');

if ($env === 'development' && file_exists(__DIR__ . '/load_env.php')) {
    include_once __DIR__ . '/load_env.php';
}

$vendorAutoload = dirname(__DIR__) . '/vendor/autoload.php';

if (!file_exists($vendorAutoload)) {
    $vendorAutoload = dirname(dirname(__DIR__)) . '/vendor/autoload.php';
}

if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
} else {
    die("Vendor autoload not found. Please run 'composer install' in the project root.");
}


// Load security module first
include_once __DIR__ . '/backend/include.php';
include_once __DIR__ . '/backend/userinfos_wrap.php';
include_once __DIR__ . '/frontend/include.php';
include_once __DIR__ . '/leaderboard/include.php';

