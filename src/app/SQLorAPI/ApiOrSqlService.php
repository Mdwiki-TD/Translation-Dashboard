<?php

namespace App\SQLorAPI;

use App\MdwikiSql\Database;
use App\Settings;
use App\Logger;

function postUrl(string $ServerUrl, array $params = []): string
{
    if (empty($params)) return "";

    $timeStart = microtime(true);
    $usrAgent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

    $ch = curl_init();

    $url = "{$ServerUrl}?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => $usrAgent,
        // CURLOPT_COOKIEJAR => "cookie.txt",
        // CURLOPT_COOKIEFILE => "cookie.txt",
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 8,
    ]);

    $output = curl_exec($ch);

    // remove "&format=json" from $url then make it link <a href="$url2">
    $url2 = str_replace('&format=json', '', $url);
    $url2 = "<a target='_blank' href='$url2'>$url2</a>";

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 200) {
        Logger::debug('postUrl: Error: API request failed with status code ' . $httpCode);
    }

    $executionTime = (microtime(true) - $timeStart);
    $executionTime = round($executionTime, 4);

    Logger::debug("postUrl (time: $executionTime s): (http_code: $httpCode) $url2");

    if ($output === FALSE) {
        Logger::debug("postUrl: cURL Error: " . curl_error($ch));
    }

    if (curl_errno($ch)) {
        Logger::debug('postUrl: Error:' . curl_error($ch));
    }

    return $output === false ? '' : $output;
}

class ApiOrSqlService
{
    private Database $db;

    public static ?bool $useTdApi = null;

    public function __construct(Database $db, ?bool $useTdApi = null)
    {
        $this->db = $db;
        self::$useTdApi = $useTdApi;
    }

    public static function getTdApi(array $params): array
    {
        $settings = Settings::getInstance();

        $ServerUrl = $settings->ServerUrl . '/api.php';

        $out = postUrl($ServerUrl, $params);

        $apiResults = json_decode($out, true);

        if (!is_array($apiResults)) {
            $apiResults = [];
        }

        $result = $apiResults['results'] ?? [];

        if (isset($result['error'])) {
            Logger::debug('Error:' . json_encode($result['error'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        return $apiResults;
    }
    public function useTdApiOrSql(): bool
    {
        if (self::$useTdApi === null) {
            // var_dump(json_encode($settingsTabe, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            // "{ "allow_type_of_translate": 0, "translation_button_in_progress_table": 1, "fix_ref_in_text": 0, "use_td_api": 1, "use_mdwikicx": 1}"
            $apiResults = self::getTdApi(['get' => 'settings']);
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

    public function superFunction(
        array $apiParams,
        array $sqlParams,
        string $sqlQuery,
        bool $noRefind = false
    ): array {
        $apiData = [];

        $useTdApi = self::useTdApiOrSql();
        if ($useTdApi) {
            $apiResults = self::getTdApi($apiParams);

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
            $apiData = $this->db->fetchQuery($sqlQuery, $sqlParams);
        }

        return $apiData;
    }
}
