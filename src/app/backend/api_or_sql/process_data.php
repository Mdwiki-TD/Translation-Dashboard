<?php

namespace App\SQLorAPI\Process;

use App\SQLorAPI\InProcess\InProcessTable;

function get_process_data(): array
{
    return InProcessTable::getProcessData();
}

function get_user_process_new(string $user, string $year_y = "all")
{
    return InProcessTable::getUserProcessNew($user, $year_y);
}

function get_users_process_new(): array
{
    return InProcessTable::getUsersProcessNew();
}

function get_lang_in_process_by_year($code, $year_y = "all"): array
{
    return InProcessTable::getLangInProcessByYear($code, $year_y);
}

function get_lang_in_process($code): array
{
    return InProcessTable::getLangInProcess($code);
}
