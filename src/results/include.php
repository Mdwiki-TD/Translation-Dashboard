<?php

include_once __DIR__ . '/../Tables/include.php';
include_once __DIR__ . '/../actions/load_request.php';


foreach (glob(__DIR__ . "/*.php") as $filename) {
    if ($filename == __FILE__) {
        continue;
    }
    include_once $filename;
}
/*
include_once __DIR__ . '/SPARQLDispatcher.php';
include_once __DIR__ . '/sparql_bot.php';
include_once __DIR__ . '/helps.php';
include_once __DIR__ . '/tr_link.php';
include_once __DIR__ . '/results_table.php';
include_once __DIR__ . '/results_table_exists.php';
include_once __DIR__ . '/fetch_cat_data.php';
include_once __DIR__ . '/fetch_cat_data_sparql.php';
include_once __DIR__ . '/cats_api.php';
include_once __DIR__ . '/get_results.php';
include_once __DIR__ . '/getcats.php';
*/
