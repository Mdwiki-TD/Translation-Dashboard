<?php

foreach (glob(__DIR__ . "/../backend/results/sparql_bots/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/../backend/results/get_titles/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/../backend/results/new_way/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/../backend/results/*.php") as $filename) {
    if (basename($filename) === "getcats_new.php") continue;
    include_once $filename;
}

foreach (glob(__DIR__ . "/*.php") as $filename) {
    // if (basename($filename) === "results.php") continue;
    if (basename($filename) === "include.php") continue;
    include_once $filename;
}

foreach (glob(__DIR__ . "/rows/*.php") as $filename) {
    include_once $filename;
}
