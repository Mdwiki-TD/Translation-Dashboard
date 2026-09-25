<?php

namespace App\SQLorAPI\Get;

use function App\APICalls\MdwikiSql\fetch_query;
use function App\APICalls\TDApi\get_td_api;

function use_td_api_or_sql(): bool
{
    static $use_td_api = null;
    if ($use_td_api === null) {
        // var_dump(json_encode($settings_tabe, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        // "{ "allow_type_of_translate": 0, "translation_button_in_progress_table": 1, "fix_ref_in_text": 0, "use_td_api": 1, "use_mdwikicx": 1}"
        $api_results = get_td_api(['get' => 'settings']);
        $data = $api_results['results'] ?? [];

        $settings_tabe = array_column($data, 'value', 'title');

        $use_td_api  = (($settings_tabe['use_td_api'] ?? "") == "1") ? true : false;

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

function super_function(
    array $api_params,
    array $sql_params,
    string $sql_query,
    bool $no_refind = false
): array {
    $api_data = [];

    $use_td_api = use_td_api_or_sql();
    if ($use_td_api) {
        $api_results = get_td_api($api_params);

        $api_data = $api_results['results'] ?? [];

        $length = $api_results['length'] ?? null;

        if ($length === 0) {
            // API return empty list. no need to check sql.
            return $api_data;
        }
    }

    if (empty($api_data) && (getenv('APP_ENV') === 'testing' || defined('PHPUNIT_RUNNING'))) {
        return [];
    }

    if (empty($api_data) && !$no_refind) {
        $api_data = fetch_query($sql_query, $sql_params);
    }

    return $api_data;
}
