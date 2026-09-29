<?php

namespace App\SQLorAPI\Get;

use App\MdwikiSql\Database;
use function App\APICalls\TDApi\get_td_api;

function fetch_query(string $sqlQuery, ?array $params = null): array
{
    // Create a new database object
    $db = new Database();

    // Execute a SQL query
    $results = $db->fetchquery($sqlQuery, $params);

    // Destroy the database object
    $db = null;
    return $results;
};

class ApiOrSqlService
{
    private static ?bool $useTdApi = null;

    public static function resetCache(): void
    {
        self::$useTdApi = null;
    }

    public static function useTdApiOrSql(): bool
    {
        if (self::$useTdApi === null) {
            // var_dump(json_encode($settingsTabe, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            // "{ "allow_type_of_translate": 0, "translation_button_in_progress_table": 1, "fix_ref_in_text": 0, "use_td_api": 1, "use_mdwikicx": 1}"
            $apiResults = get_td_api(['get' => 'settings']);
            $data = $apiResults['results'] ?? [];

            $settingsTabe = array_column($data, 'value', 'title');

            self::$useTdApi = (($settingsTabe['use_td_api'] ?? "") == "1");
        }
        return self::$useTdApi;
    }

    public static function isValid(mixed $str): bool
    {
        return !empty($str) && strtolower((string)$str) != "all";
    }

    public static function superFunction(
        array $apiParams,
        array $sqlParams,
        string $sqlQuery,
        bool $noRefind = false
    ): array {
        $apiData = [];

        $useTdApi = self::useTdApiOrSql();
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
}
