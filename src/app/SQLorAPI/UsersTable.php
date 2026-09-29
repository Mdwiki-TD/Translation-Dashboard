<?php

namespace App\SQLorAPI;

use App\SQLorAPI\Get\ApiOrSqlService;

class UsersTable
{
    private static array $coordinatorsCache = [];
    private static array $usersNoInprocessCache = [];
    private static array $fullTranslatorsCache = [];

    public static function resetCache(): void
    {
        self::$coordinatorsCache = [];
        self::$usersNoInprocessCache = [];
        self::$fullTranslatorsCache = [];
    }

    public static function getCoordinators(): array
    {
        if (!empty(self::$coordinatorsCache)) {
            return self::$coordinatorsCache;
        }

        $apiParams = ['get' => 'coordinators'];
        $query = "SELECT id, username, is_active FROM coordinators order by id";

        self::$coordinatorsCache = ApiOrSqlService::superFunction($apiParams, [], $query);

        return self::$coordinatorsCache;
    }

    public static function getUsersNoInprocess(): array
    {
        if (!empty(self::$usersNoInprocessCache)) {
            return self::$usersNoInprocessCache;
        }

        $apiParams = ['get' => 'users_no_inprocess'];
        $query = "SELECT id, user, is_active FROM users_no_inprocess order by id";

        self::$usersNoInprocessCache = ApiOrSqlService::superFunction($apiParams, [], $query);

        return self::$usersNoInprocessCache;
    }

    public static function getFullTranslators(mixed $column = null): array
    {
        if (empty(self::$fullTranslatorsCache)) {
            $apiParams = ['get' => 'full_translators'];
            $query = "SELECT id, user, is_active FROM full_translators";
            self::$fullTranslatorsCache = ApiOrSqlService::superFunction($apiParams, [], $query);
        }

        if ($column) {
            return array_column(self::$fullTranslatorsCache, $column);
        }

        return self::$fullTranslatorsCache;
    }
}


function getUsersNoInprocess()
{
    return UsersTable::getUsersNoInprocess();
}

function getFullTranslators($column = null)
{
    return UsersTable::getFullTranslators($column);
}

function getCoordinators()
{
    return UsersTable::getCoordinators();
}
