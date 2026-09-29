<?php

namespace App\SQLorAPI;

use App\SQLorAPI\BaseTable;

class UsersTable extends BaseTable
{
    private static array $coordinatorsCache = [];
    private static array $usersNoInprocessCache = [];
    private static array $fullTranslatorsCache = [];


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
        self::$coordinatorsCache = [];
        self::$usersNoInprocessCache = [];
        self::$fullTranslatorsCache = [];
    }

    public function getCoordinators(): array
    {
        if (!empty(self::$coordinatorsCache)) {
            return self::$coordinatorsCache;
        }

        $apiParams = ['get' => 'coordinators'];
        $query = "SELECT id, username, is_active FROM coordinators order by id";

        self::$coordinatorsCache = $this->service->superFunction($apiParams, [], $query);

        return self::$coordinatorsCache;
    }

    public function getUsersNoInprocess(): array
    {
        if (!empty(self::$usersNoInprocessCache)) {
            return self::$usersNoInprocessCache;
        }

        $apiParams = ['get' => 'users_no_inprocess'];
        $query = "SELECT id, user, is_active FROM users_no_inprocess order by id";

        self::$usersNoInprocessCache = $this->service->superFunction($apiParams, [], $query);

        return self::$usersNoInprocessCache;
    }

    public function getFullTranslators(mixed $column = null): array
    {
        if (empty(self::$fullTranslatorsCache)) {
            $apiParams = ['get' => 'full_translators'];
            $query = "SELECT id, user, is_active FROM full_translators order by id";
            self::$fullTranslatorsCache = $this->service->superFunction($apiParams, [], $query);
        }

        if ($column) {
            return array_column(self::$fullTranslatorsCache, $column);
        }

        return self::$fullTranslatorsCache;
    }

    public function getUsersByLastPupdate(): array
    {
        static $lastUserToTab = [];

        if (!empty($lastUserToTab ?? [])) {
            return $lastUserToTab;
        }

        $data = [];

        $apiParams = array('get' => 'users_by_last_pupdate');
        $queryOld = <<<SQL
            select DISTINCT p1.target, p1.title, p1.user, p1.pupdate, p1.lang
            from pages p1
            where target != ''
            and p1.pupdate = (select p2.pupdate from pages p2 where p2.user = p1.user ORDER BY p2.pupdate DESC limit 1)
            group by p1.user
            ORDER BY p1.pupdate DESC
        SQL;

        $query = <<<SQL
            WITH RankedPages AS (
                SELECT
                    p1.target,
                    p1.user,
                    p1.pupdate,
                    p1.lang,
                    p1.title,
                    ROW_NUMBER() OVER (PARTITION BY p1.user ORDER BY p1.pupdate DESC) AS rn
                FROM pages p1
                WHERE p1.target != ''
            )
            SELECT target, user, pupdate, lang, title
            FROM RankedPages
            WHERE rn = 1
            ORDER BY pupdate DESC;
        SQL;

        $data = $this->service->superFunction($apiParams, [], $query);

        foreach ($data as $key => $gg) {
            $lastUserToTab[$gg['user']] = $gg;
        }

        return $lastUserToTab;
    }

}
