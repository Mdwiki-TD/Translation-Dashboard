<?php

namespace App\SQLorAPI;

use App\SQLorAPI\ApiOrSqlService;

class SettingsTable
{
    private static array $sqlSettingsCache = [];

    private ApiOrSqlService $service;
    public function __construct(?ApiOrSqlService $service = null)
    {
        $this->service = $service ?? new ApiOrSqlService();
    }

    public static function resetCache(): void
    {
        self::$sqlSettingsCache = [];
    }

    public function getSettings(): array
    {
        if (!empty(self::$sqlSettingsCache)) {
            return self::$sqlSettingsCache;
        }

        $query = "select id, title, displayed, value, Type from settings";
        $apiParams = ['get' => 'settings'];

        self::$sqlSettingsCache = $this->service->superFunction($apiParams, [], $query);

        return self::$sqlSettingsCache;
    }

    public function getEndpointOld(): string
    {
        $settings1 = self::getSettings();
        $settings1Map = array_column($settings1, 'value', 'title');

        $useMdwikicx = $settings1Map['use_mdwikicx'] ?? '0';

        $endpoint = "https://medwiki.toolforge.org/w/index.php";

        if ($useMdwikicx != '0') {
            $endpoint = "https://mdwikicx.toolforge.org/w/index.php";
        }

        return $endpoint;
    }

    public function getEndpoint(): string
    {
        return "https://mdwikicx.toolforge.org/w/index.php";
    }
}

function getSettings()
{
    return (new SettingsTable())->getSettings();
}

function getEndpoint()
{
    return (new SettingsTable())->getEndpoint();
}
