<?php

namespace App\SQLorAPI;
use App\SQLorAPI\ApiOrSqlService;

class BaseTable
{
    public ApiOrSqlService $service;
    public function __construct(?ApiOrSqlService $service = null)
    {
        // set $useTdApi to null in td repo
        $this->service = $service ?? new ApiOrSqlService();
    }

}
