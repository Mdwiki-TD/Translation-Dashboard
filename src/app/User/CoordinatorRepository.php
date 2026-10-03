<?php
// src/app/User/CoordinatorRepository.php

namespace App\User;

use App\MdwikiSql\Database;

/**
 * Owns the coordinators lookup. Kept separate from user/access concerns
 * since it's a distinct table with its own meaning (authorization role,
 * not identity).
 */
class CoordinatorRepository
{
    public function __construct(private Database $db)
    {
    }

    public function isCoordinator(string $username): bool
    {
        if ($username === "") {
            return false;
        }

        $query = "SELECT id, username, is_active FROM coordinators order by id";
        $dbResult = $this->db->fetchQuery($query);

        $coordinators = array_column($dbResult, "is_active", "username");

        return (($coordinators[$username] ?? 0) == 1);
    }
}
