<?php

namespace App\SQLorAPI\GetDataTab;

use function App\SQLorAPI\Get\superFunction;

function getSettings()
{

    static $sqlSettings = [];

    if (!empty($sqlSettings)) {
        return $sqlSettings;
    }

    $query = "select id, title, displayed, value, Type from settings";

    $apiParams = ['get' => 'settings'];

    $sqlSettings = ApiOrSqlService::superFunction($apiParams, [], $query);

    return $sqlSettings;
}
