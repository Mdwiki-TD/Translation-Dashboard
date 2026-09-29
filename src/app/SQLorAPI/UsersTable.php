<?php

namespace App\SQLorAPI;

use App\SQLorAPI\ApiOrSqlService;

class UsersTable
{
    private static array $coordinatorsCache = [];
    private static array $usersNoInprocessCache = [];
    private static array $fullTranslatorsCache = [];

    private ApiOrSqlService $service;
    public function __construct(?ApiOrSqlService $service = null)
    {
        $this->service = $service ?? new ApiOrSqlService();
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
            $query = "SELECT id, user, is_active FROM full_translators";
            self::$fullTranslatorsCache = $this->service->superFunction($apiParams, [], $query);
        }

        if ($column) {
            return array_column(self::$fullTranslatorsCache, $column);
        }

        return self::$fullTranslatorsCache;
    }
}


function getUsersNoInprocess()
{
    return (new UsersTable())->getUsersNoInprocess();
}

function getFullTranslators($column = null)
{
    return (new UsersTable())->getFullTranslators($column);
}

function getCoordinators()
{
    return (new UsersTable())->getCoordinators();
}
