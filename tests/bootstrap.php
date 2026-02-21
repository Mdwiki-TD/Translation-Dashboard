<?php

declare(strict_types=1);

// Set test environment
putenv('APP_ENV=testing');
require_once __DIR__ . '/../src/include_all.php';

$vendorAutoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}
