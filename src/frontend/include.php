<?php

/*
include_once __DIR__ . '/backend/include_first/include.php';
*/
foreach (glob(__DIR__ . "/*.php") as $filename) {
    if ($filename == __FILE__) continue;
    include_once $filename;
}

foreach (glob(__DIR__ . "/results_rows/*.php") as $filename) {
    include_once $filename;
}
