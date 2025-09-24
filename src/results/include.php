<?php

foreach (glob(__DIR__ . "/sparql_bots/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/*.php") as $filename) {
    // if (basename($filename) === "results.php") continue;
    if (basename($filename) === "include.php") continue;
    include_once $filename;
}
