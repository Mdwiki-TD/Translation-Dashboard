<?php

declare(strict_types=1);

// Set test environment
putenv('APP_ENV=testing');
putenv('DB_HOST_TOOLS=localhost:3306');
putenv('DB_NAME=s54732__mdwiki');
putenv('DB_NAME_NEW=s54732__mdwiki_new');
putenv('TOOL_TOOLSDB_USER=root');
putenv('TOOL_TOOLSDB_PASSWORD=root11');

require_once __DIR__ . '/../src/include_all.php';

$vendorAutoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}
