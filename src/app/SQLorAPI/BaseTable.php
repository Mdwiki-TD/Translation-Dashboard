<?php

namespace App\SQLorAPI;
use App\SQLorAPI\ApiOrSqlService;

class BaseTable
{
    public ApiOrSqlService $service;
    public function __construct(?ApiOrSqlService $service = null)
    {
        $this->service = $service ?? new ApiOrSqlService();
    }

}
