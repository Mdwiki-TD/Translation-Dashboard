<?php

//display errors

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/wd.php';

use function Publish\WD\LinkToWikidata;

$do = LinkToWikidata("Ciprofloxacin", "ar", "Mr. Ibrahem", "Ciprofloxacin", "", "");

echo "LinkToWikidata:";
echo json_encode($do, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
