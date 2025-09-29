<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/include_all.php';

$vendorAutoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}
