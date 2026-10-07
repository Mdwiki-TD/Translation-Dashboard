<?php

namespace App\SQLorAPI;

use App\SQLorAPI\BaseTable;

class UsersLeaderboardTable extends BaseTable
{
    private static array $userDataCache = [];


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
        self::$userDataCache = [];
    }

    public function getUserYears(string $user): array
    {
        $apiParams = ['get' => 'user_status', 'select' => 'year', 'user' => $user];
        $query = "SELECT DISTINCT YEAR(p.date) AS year FROM pages p WHERE p.user = ?";
        $params = [$user];

        $uData = $this->service->superFunction($apiParams, $params, $query);
        $uData = array_map('current', $uData);

        // remove empty or null years
        $uData = array_filter($uData, function ($value) {
            return !empty($value);
        });

        // sort years
        rsort($uData);

        return $uData;
    }

    public function getUserLangs(string $user): array
    {
        $apiParams = ['get' => 'user_status', 'select' => 'lang', 'user' => $user];
        $query = "SELECT DISTINCT p.lang FROM pages p WHERE p.user = ?";
        $params = [$user];

        $uData = $this->service->superFunction($apiParams, $params, $query);
        $uData = array_map('current', $uData);

        // remove empty or null years
        $uData = array_filter($uData, function ($value) {
            return !empty($value);
        });

        return $uData;
    }

    public function getUserCamps(string $user): array
    {
        $apiParams = ['get' => 'user_status', 'select' => 'campaign', 'user' => $user];
        $query = "SELECT DISTINCT ca.campaign
            FROM pages p
            LEFT JOIN categories ca ON p.cat = ca.category
            WHERE p.user = ?
        ";
        $params = [$user];

        $uData = $this->service->superFunction($apiParams, $params, $query);
        $uData = array_map('current', $uData);

        // remove empty or null years
        $uData = array_filter($uData, function ($value) {
            return !empty($value);
        });

        return $uData;
    }

    public function getUserFilterData(string $user): array
    {
        $key = (string)$user;
        if (!empty(self::$userDataCache[$key] ?? [])) {
            return self::$userDataCache[$key];
        }

        $years = $this->getUserYears($user);
        $langs = $this->getUserLangs($user);
        $camps = $this->getUserCamps($user);

        $uData = [
            "years" => $years,
            "langs" => $langs,
            "camps" => $camps,
        ];
        self::$userDataCache[$key] = $uData;

        return $uData;
    }
    private function formatResult(array $rows): array
    {
        $uData = [
            "years" => [],
            "langs" => [],
            "camps" => [],
        ];

        foreach ($rows as $row) {
            $year      = $row['year'] ?? '';
            $lang      = $row['lang'] ?? '';
            $campaign  = $row['campaign'] ?? '';

            foreach (['years' => $year, 'langs' => $lang, 'camps' => $campaign] as $bucket => $key) {
                $uData[$bucket][$key] ??= 0;
                $uData[$bucket][$key] += 1;
            }
        }
        return $uData;
    }

    public function getUserNewFilterData(string $user): array
    {
        $apiParams = ['get' => 'user_data_status', 'user' => $user];

        $apiResult = $this->service->superFunction($apiParams, [], "");
        // apiResult already formatted into years/langs/camps
        if (!empty($apiResult)) {
            return $apiResult;
        }

        $sql = "SELECT YEAR(p.pupdate) AS year, p.lang, ca.campaign
            FROM pages p
            LEFT JOIN categories ca ON p.cat = ca.category
            WHERE p.user = ?
        ";
        $params = [$user];

        $result = $this->service->superFunction([], $params, $sql);
        // $result example: [ { "year": 2021, "lang": "ar", "campaign": "Main" }, ... ]

        return $this->formatResult($result);
    }
}
