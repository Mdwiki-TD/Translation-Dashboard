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

function use_td_api_or_sql()
{
    static $use_td_api = null;
    if ($use_td_api === null) {
        $settings_tabe = array_column(get_td_api(['get' => 'settings']), 'value', 'title');
        // var_dump(json_encode($settings_tabe, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        // "{ "allow_type_of_translate": 0, "translation_button_in_progress_table": 1, "fix_ref_in_text": 0, "use_medwiki": 1, "use_td_api": 1, "use_mdwikicx": 1, "load_new_result": 0 }"
        $use_td_api  = (($settings_tabe['use_td_api'] ?? "") == "1") ? true : false;
        // ---
        if (isset($_GET['use_td_api'])) {
            $use_td_api  = $_GET['use_td_api'] != "x";
        }
    }
    return $use_td_api;
}


function isvalid($str)
{
    return !empty($str) && strtolower($str) != "all";
}

function super_function(array $api_params, array $sql_params, string $sql_query, $table_name = null, $no_refind = false): array
{
    $use_td_api = use_td_api_or_sql();
    // ---
    $data = ($use_td_api) ? get_td_api($api_params) : [];
    // ---
    if (empty($data) && !$no_refind) {
        test_print("<br> >>>>> Query:");
        $data = fetch_query($sql_query, $sql_params, $table_name);
    }
    // ---
    return $data;
}
