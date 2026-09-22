<?php

declare(strict_types=1);

// Set test environment
putenv('APP_ENV=testing');
putenv('DB_HOST_TOOLS=localhost:3306');
putenv('DB_NAME=s54732__mdwikiz');

putenv('TOOL_TOOLSDB_USER=root');
putenv('TOOL_TOOLSDB_PASSWORD=root11');
// $_SERVER['SERVER_NAME'] = 'localhost';

require_once dirname(__DIR__) . '/src/app/include_all.php';

$vendorAutoload = dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}
