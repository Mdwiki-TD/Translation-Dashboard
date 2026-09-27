<?php

namespace App\SQLorAPI\Get;

function use_td_api_or_sql(): bool
{
    return ApiOrSqlService::useTdApiOrSql();
}

function isvalid($str)
{
    return ApiOrSqlService::isValid($str);
}

function super_function(
    array $apiParams,
    array $sqlParams,
    string $sqlQuery,
    bool $noRefind = false
): array {
    return ApiOrSqlService::superFunction($apiParams, $sqlParams, $sqlQuery, $noRefind);
}
