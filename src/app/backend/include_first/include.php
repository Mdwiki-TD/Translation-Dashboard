<?php

foreach (glob(__DIR__ . "/*.php") as $filename) {
    if ($filename == __FILE__) continue;
    include_once $filename;
}
