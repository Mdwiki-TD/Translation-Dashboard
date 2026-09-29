<?php

namespace App\SQLorAPI\Users;

use App\SQLorAPI\Get\ApiOrSqlService;

function getCoordinators()
{

    static $coordinators = [];

    if (!empty($coordinators ?? [])) {
        return $coordinators;
    }

    $apiParams = ['get' => 'coordinators'];
    $query = "SELECT id, username, is_active FROM coordinators order by id";

    $uData = superFunction($apiParams, [], $query);

    $coordinators = $uData;

    return $coordinators;
}
