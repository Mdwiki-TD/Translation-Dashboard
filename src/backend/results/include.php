<?php

foreach (glob(__DIR__ . "/sparql_bots/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/get_titles/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/new_way/*.php") as $filename) {
    include_once $filename;
}

foreach (glob(__DIR__ . "/*.php") as $filename) {
    if ($filename == __FILE__) continue;
    if (basename($filename) === "getcats_new.php") continue;
    include_once $filename;
}

foreach (glob(__DIR__ . "/rows/*.php") as $filename) {
    include_once $filename;
}
