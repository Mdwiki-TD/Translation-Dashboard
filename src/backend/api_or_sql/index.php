<?php

namespace SQLorAPI\Get;

/*

Usage:

use function SQLorAPI\Get\super_function;
use function SQLorAPI\Get\isvalid;

*/

use function APICalls\MdwikiSql\fetch_query;
use function APICalls\TDApi\get_td_api;
use function TD\Render\TestPrint\test_print;

$settings_tabe = array_column(get_td_api(['get' => 'settings']), 'value', 'title');
//---
$use_td_api  = (($settings_tabe['use_td_api'] ?? "") == "1") ? true : false;
// ---
if (isset($_GET['use_td_api'])) {
    $use_td_api  = $_GET['use_td_api'] != "x";
}

function isvalid($str)
{
    return !empty($str) && strtolower($str) != "all";
}

function super_function(array $api_params, array $sql_params, string $sql_query, $no_refind = false, $table_name = null): array
{
    global $use_td_api;
    // ---
    $data = ($use_td_api) ? get_td_api($api_params) : [];
    // ---
    if (empty($data) && !$no_refind) {
        test_print("<br> >>>>> Query:");
        $data = fetch_query($sql_query, $sql_params, $table_name = $table_name);
    }
    // ---
    return $data;
}
