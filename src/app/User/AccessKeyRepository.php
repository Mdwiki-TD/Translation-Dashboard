<?php
// src/app/User/AccessKeyRepository.php

namespace App\User;

use App\Settings;
use App\MdwikiSql\Database;

/**
 * Owns all database access related to users and their OAuth access keys.
 * No cookie/session logic, no coordinator logic.
 */
class AccessKeyRepository
{
    public function __construct(
        private Database $db,
        private Settings $settings
    ) {
    }

    private function hashUsername(string $user): string
    {
        return hash("sha256", $user);
    }

    /**
     * @return array{access_key:string, access_secret:string}|array{}
     */
    public function findByUser(string $user): array
    {
        $user = trim($user);

        $query = <<<SQL
            SELECT access_key, access_secret
            FROM access_keys
            WHERE user_name = ? or user_name_hash = ?;
        SQL;

        $result = $this->db->fetchQuery($query, [$user, $this->hashUsername($user)]);

        if (!$result) {
            return [];
        }

        $cryptKey = $this->settings->getKey("crypt");
        return [
            "access_key"    => $this->settings->decodeValue($result[0]["access_key"], $cryptKey),
            "access_secret" => $this->settings->decodeValue($result[0]["access_secret"], $cryptKey),
        ];
    }

    public function ensureUserExists(string $userName): bool
    {
        $query = <<<SQL
            INSERT INTO users (username) SELECT ?
            WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = ?)
        SQL;

        return $this->db->executeQuery($query, [$userName, $userName]);
    }

    public function upsertAccess(string $user, string $accessKey, string $accessSecret): bool
    {
        $cryptKey = $this->settings->getKey("crypt");

        $params = [
            $user,
            $this->hashUsername($user),
            $this->settings->encodeValue($accessKey, $cryptKey),
            $this->settings->encodeValue($accessSecret, $cryptKey),
        ];

        // ---
        // user_name_hash = SHA2(user_name, 256)
        // ---
        $query = <<<SQL
            INSERT INTO access_keys (user_name, user_name_hash, access_key, access_secret)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                access_key = VALUES(access_key),
                access_secret = VALUES(access_secret),
                updated_at = NOW();
        SQL;

        return $this->db->executeQuery($query, $params);
    }

    /**
     * Ensures the user row exists and writes/updates their access keys,
     * wrapped in a transaction so a partial failure doesn't leave
     * inconsistent rows.
     *
     * @throws \RuntimeException on failure
     */
    public function saveUserData(string $user, string $accessKey, string $accessSecret): void
    {
        $user = trim($user);

        $this->db->beginTransaction();

        try {
            $userAdded   = $this->ensureUserExists($user);
            $accessAdded = $this->upsertAccess($user, $accessKey, $accessSecret);

            if (!$userAdded || !$accessAdded) {
                throw new \RuntimeException("Failed to write user data or access keys to database.");
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
