<?php

namespace App\SQLorAPI\Get;

use function App\APICalls\TDApi\get_td_api;

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
            $apiResults = get_td_api(['get' => 'settings']);
            $data = $apiResults['results'] ?? [];

            $settingsTabe = array_column($data, 'value', 'title');

            self::$useTdApi = (($settingsTabe['use_td_api'] ?? "") == "1");

            if (isset($_GET['use_td_api'])) {
                self::$useTdApi = $_GET['use_td_api'] != "x";
            }
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
