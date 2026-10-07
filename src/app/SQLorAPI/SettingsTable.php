<?php

namespace App\SQLorAPI;

use App\SQLorAPI\BaseTable;

class SettingsTable extends BaseTable
{
    private static array $sqlSettingsCache = [];

    private static ?self $instance = null;
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
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

        $query = "select id, title, displayed, value, Type, ignored from settings";
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

    public function getLanguageSettings(): array
    {

        // language_settings (lang_code, move_dots, expend, add_en_lang)
        static $dataLangs = [];

        if (!empty($dataLangs)) return $dataLangs;

        $sqlParams = [];
        $apiParams = ['get' => 'language_settings'];
        $query = "SELECT * FROM language_settings order by lang_code";

        $dataLangs = $this->service->superFunction($apiParams, $sqlParams, $query);

        return $dataLangs;
    }

}
