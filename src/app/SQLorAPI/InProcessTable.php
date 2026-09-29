<?php

namespace App\SQLorAPI\InProcess;

use App\SQLorAPI\Get\ApiOrSqlService;

class InProcessTable
{
    private static array $processAllCache = [];
    private static array $userProcessCache = [];
    private static array $usersProcessCache = [];
    private static array $langYearProcessCache = [];
    private static array $langProcessCache = [];

    public static function resetCache(): void
    {
        self::$processAllCache = [];
        self::$userProcessCache = [];
        self::$usersProcessCache = [];
        self::$langYearProcessCache = [];
        self::$langProcessCache = [];
    }

    public static function getProcessData(): array
    {
        if (!empty(self::$processAllCache)) {
            return self::$processAllCache;
        }

        $apiParams = ['get' => 'in_process', 'limit' => "100", "order" => 'add_date'];
        $sql_t = "select * from in_process ORDER BY add_date DESC limit 100";

        self::$processAllCache = ApiOrSqlService::superFunction($apiParams, [], $sql_t);

        return self::$processAllCache;
    }

    public static function getUserProcessNew(string $user, string $yearY = "all"): array
    {
        if (!empty(self::$userProcessCache[$user] ?? [])) {
            return self::$userProcessCache[$user];
        }

        $apiParams = ['get' => 'in_process', 'user' => $user];
        $query = "select * from in_process where user = ?";
        $params = [$user];

        if (ApiOrSqlService::isValid($yearY)) {
            $query .= " AND YEAR(add_date) = ?";
            $params[] = $yearY;
            $apiParams['year'] = $yearY;
        }

        $data = ApiOrSqlService::superFunction($apiParams, $params, $query, true);
        self::$userProcessCache[$user] = $data;

        return $data;
    }

    public static function getUsersProcessNew(): array
    {
        if (!empty(self::$usersProcessCache)) {
            return self::$usersProcessCache;
        }

        $apiParams = ['get' => 'in_process', 'distinct' => 'true', "select" => 'user', 'group' => 'user', "order" => '2', "count" => '*'];
        $sql_t = 'select DISTINCT user, count(*) as count from in_process group by user order by count desc';

        $tab = ApiOrSqlService::superFunction($apiParams, [], $sql_t);
        self::$usersProcessCache = array_column($tab, 'count', 'user');

        return self::$usersProcessCache;
    }

    public static function getLangInProcessByYear(mixed $code, string $yearY = "all"): array
    {
        $codeStr = (string)$code;
        if (!empty(self::$langYearProcessCache[$codeStr][$yearY] ?? [])) {
            return self::$langYearProcessCache[$codeStr][$yearY];
        }

        $query = "select * from in_process where lang = ?";
        $apiParams = ['get' => 'in_process', 'lang' => $codeStr];
        $params = [$codeStr];

        if (ApiOrSqlService::isValid($yearY)) {
            $query .= " AND YEAR(add_date) = ?";
            $params[] = $yearY;
            $apiParams['year'] = $yearY;
        }

        $data = ApiOrSqlService::superFunction($apiParams, $params, $query);
        self::$langYearProcessCache[$codeStr][$yearY] = $data;

        return self::$langYearProcessCache[$codeStr][$yearY];
    }

    public static function getLangInProcess(mixed $code): array
    {
        $codeStr = (string)$code;
        if (!empty(self::$langProcessCache[$codeStr] ?? [])) {
            return self::$langProcessCache[$codeStr];
        }

        $query = "select * from in_process where lang = ?";
        $apiParams = ['get' => 'in_process', 'lang' => $codeStr];
        $params = [$codeStr];

        $data = ApiOrSqlService::superFunction($apiParams, $params, $query);
        self::$langProcessCache[$codeStr] = $data;

        return $data;
    }
}

function getProcessData(): array
{
    return InProcessTable::getProcessData();
}

function getUserProcessNew(string $user, string $year_y = "all")
{
    return InProcessTable::getUserProcessNew($user, $year_y);
}

function getUsersProcessNew(): array
{
    return InProcessTable::getUsersProcessNew();
}

function getLangInProcessByYear($code, $year_y = "all"): array
{
    return InProcessTable::getLangInProcessByYear($code, $year_y);
}

function getLangInProcess($code): array
{
    return InProcessTable::getLangInProcess($code);
}
