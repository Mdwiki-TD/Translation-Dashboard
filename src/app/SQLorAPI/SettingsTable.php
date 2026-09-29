<?php

namespace App\SQLorAPI\Settings;

use App\SQLorAPI\Get\ApiOrSqlService;

class SettingsTable
{
    private static array $sqlSettingsCache = [];

    public static function resetCache(): void
    {
        self::$sqlSettingsCache = [];
    }

    public static function getTdOrSqlSettings(): array
    {
        if (!empty(self::$sqlSettingsCache)) {
            return self::$sqlSettingsCache;
        }

        $query = "select id, title, displayed, value, Type from settings";
        $apiParams = ['get' => 'settings'];

        self::$sqlSettingsCache = ApiOrSqlService::superFunction($apiParams, [], $query);

        return self::$sqlSettingsCache;
    }

    public static function getEndpointOld(): string
    {
        $settings1 = self::getTdOrSqlSettings();
        $settings1Map = array_column($settings1, 'value', 'title');

        $useMdwikicx = $settings1Map['use_mdwikicx'] ?? '0';

        $endpoint = "https://medwiki.toolforge.org/w/index.php";

        if ($useMdwikicx != '0') {
            $endpoint = "https://mdwikicx.toolforge.org/w/index.php";
        }

        return $endpoint;
    }

    public static function getEndpoint(): string
    {
        return "https://mdwikicx.toolforge.org/w/index.php";
    }
}
