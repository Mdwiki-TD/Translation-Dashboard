<?php

namespace App\SQLorAPI;

use App\SQLorAPI\BaseTable;

class InProcessTable extends BaseTable
{
    private static array $processAllCache = [];
    private static array $userProcessCache = [];
    private static array $usersProcessCache = [];
    private static array $langYearProcessCache = [];
    private static array $langProcessCache = [];

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
        self::$processAllCache = [];
        self::$userProcessCache = [];
        self::$usersProcessCache = [];
        self::$langYearProcessCache = [];
        self::$langProcessCache = [];
    }

    public function getProcessData(): array
    {
        if (!empty(self::$processAllCache)) {
            return self::$processAllCache;
        }

        $apiParams = ['get' => 'in_process', 'limit' => "100", "order" => 'add_date'];
        $sql_t = "select * from in_process ORDER BY add_date DESC limit 100";

        self::$processAllCache = $this->service->superFunction($apiParams, [], $sql_t);

        return self::$processAllCache;
    }

    public function getUserProcessNew(string $user, string $yearY = "all"): array
    {
        $key = $this->service->isValid($yearY)
            ? "{$user}_{$yearY}"
            : $user;

        if (!empty(self::$userProcessCache[$key] ?? [])) {
            return self::$userProcessCache[$key];
        }

        $apiParams = ['get' => 'in_process', 'user' => $user];
        $query = "select * from in_process where user = ?";
        $params = [$user];

        if ($this->service->isValid($yearY)) {
            $query .= " AND YEAR(add_date) = ?";
            $params[] = $yearY;
            $apiParams['year'] = $yearY;
        }

        $data = $this->service->superFunction($apiParams, $params, $query, true);
        self::$userProcessCache[$key] = $data;

        return $data;
    }

    public function getUsersProcessNew(): array
    {
        if (!empty(self::$usersProcessCache)) {
            return self::$usersProcessCache;
        }

        $apiParams = ['get' => 'in_process', 'distinct' => 'true', "select" => 'user', 'group' => 'user', "order" => '2', "count" => '*'];

        $sql_t = 'select DISTINCT user, count(*) as count from in_process group by user order by count desc';

        $tab = $this->service->superFunction($apiParams, [], $sql_t);
        self::$usersProcessCache = array_column($tab, 'count', 'user');

        return self::$usersProcessCache;
    }

    public function getLangInProcessByYear(string $code, string $yearY = "all"): array
    {
        $codeStr = (string)$code;
        if (!empty(self::$langYearProcessCache[$codeStr][$yearY] ?? [])) {
            return self::$langYearProcessCache[$codeStr][$yearY];
        }
        /*
        SELECT * from in_process ip
            WHERE NOT EXISTS (
            SELECT p.user FROM pages p
            where p.title = ip.title
            and p.lang = ip.lang
            and p.target != ""
            )
        */

        $query = "select * from in_process where lang = ?";
        $apiParams = ['get' => 'in_process', 'lang' => $codeStr];
        $params = [$codeStr];

        if ($this->service->isValid($yearY)) {
            $query .= " AND YEAR(add_date) = ?";
            $params[] = $yearY;
            $apiParams['year'] = $yearY;
        }

        $data = $this->service->superFunction($apiParams, $params, $query, true);
        self::$langYearProcessCache[$codeStr][$yearY] = $data;

        return self::$langYearProcessCache[$codeStr][$yearY];
    }

    public function getLangInProcess(string $code): array
    {
        $codeStr = (string)$code;
        if (!empty(self::$langProcessCache[$codeStr] ?? [])) {
            return self::$langProcessCache[$codeStr];
        }

        $query = "select * from in_process where lang = ?";
        $apiParams = ['get' => 'in_process', 'lang' => $codeStr];
        $params = [$codeStr];

        $data = $this->service->superFunction($apiParams, $params, $query);
        self::$langProcessCache[$codeStr] = $data;

        return $data;
    }
}
