<?php

namespace App\SQLorAPI\Get;

use function App\MdwikiSql\fetch_query;
use function App\APICalls\TDApi\get_td_api;

function use_td_api_or_sql(): bool
{
    static $useTdApi = null;
    if ($useTdApi === null) {
        // var_dump(json_encode($settingsTabe, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        // "{ "allow_type_of_translate": 0, "translation_button_in_progress_table": 1, "fix_ref_in_text": 0, "use_td_api": 1, "use_mdwikicx": 1}"
        $apiResults = get_td_api(['get' => 'settings']);
        $data = $apiResults['results'] ?? [];

        $settingsTabe = array_column($data, 'value', 'title');

        $useTdApi  = (($settingsTabe['use_td_api'] ?? "") == "1") ? true : false;

        if (isset($_GET['use_td_api'])) {
            $useTdApi  = $_GET['use_td_api'] != "x";
        }
    }
    return $useTdApi;
}

function isvalid($str)
{
    return !empty($str) && strtolower($str) != "all";
}

function super_function(
    array $apiParams,
    array $sqlParams,
    string $sqlQuery,
    bool $noRefind = false
): array {
    $apiData = [];

    $useTdApi = use_td_api_or_sql();
    if ($useTdApi) {
        $apiResults = get_td_api($apiParams);

        $apiData = $apiResults['results'] ?? [];

        $length = $apiResults['length'] ?? null;

        if ($length === 0) {
            // API return empty list. no need to check sql.
            return $apiData;
        }
    }

    if (empty($apiData) && (getenv('APP_ENV') === 'testing' || defined('PHPUNIT_RUNNING'))) {
        return [];
    }

    if (empty($apiData) && !$noRefind) {
        $apiData = fetch_query($sqlQuery, $sqlParams);
    }

    return $apiData;
}
