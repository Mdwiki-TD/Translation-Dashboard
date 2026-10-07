<?php

namespace App\SQLorAPI;

use App\SQLorAPI\ApiOrSqlService;
use App\MdwikiSql\Database;

class BaseTable
{
    public Database $db;
    public ApiOrSqlService $service;
    public function __construct(?ApiOrSqlService $service = null)
    {
        $this->db = new Database();
        // set $useTdApi to null in td repo
        $this->service = $service ?? ApiOrSqlService::getInstance($this->db);
    }
}
