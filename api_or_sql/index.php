<?php

namespace SQLorAPI\Get;

/*

Usage:

use function SQLorAPI\Get\super_function;
use function SQLorAPI\Get\isvalid;

*/

use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;

$settings_tabe = array_column(get_td_api(['get' => 'settings']), 'value', 'title');
//---
$use_td_api  = (($settings_tabe['use_td_api'] ?? "") == "1") ? true : false;
// ---
if (isset($_GET['use_td_api'])) {
    $use_td_api  = $_GET['use_td_api'] != "x";
}

include_once __DIR__ . '/get_lead.php';
include_once __DIR__ . '/data_tab.php';
include_once __DIR__ . '/process_data.php';
include_once __DIR__ . '/funcs.php';

$data_index = [];

function isvalid($str)
{
    return !empty($str) && strtolower($str) != "all";
}

function super_function($api_params, $sql_params, $sql_query)
{
    global $use_td_api;
    // ---
    $data = ($use_td_api) ? get_td_api($api_params) : [];
    // ---
    if (empty($data)) {
        $data = fetch_query($sql_query, $sql_params);
    }
    // ---
    return $data;
}
