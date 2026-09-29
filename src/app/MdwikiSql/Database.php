<?php
// src/app/Database.php

/**
 * Database Abstraction Layer for MDWiki SQL Operations
 *
 */

namespace App\MdwikiSql;

use App\Logger;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Database Connection and Query Management Class
 *
 * Encapsulates PDO database operations with automatic connection management,
 * error handling, and environment-specific configuration.
 *
 * @package MdwikiSql
 */
class Database
{

    private $db;
    private $host;
    private $user;
    private $password;
    private $dbname;
    private $appEnv;
    private $groupByModeDisabled = false;

    public function __construct(string $dbnameVar = 'DB_NAME')
    {
        $this->appEnv = $this->envVar('APP_ENV');
        $this->setDb($dbnameVar);
    }

    private function envVar(string $key)
    {
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        return "";
    }
    private function buildDsn(string $dbnameVar): string
    {
        // Load host and database name from environment variables, falling back to a default host
        $this->host   = $this->envVar('DB_HOST_TOOLS') ?: 'tools.db.svc.wikimedia.cloud';
        $this->dbname = $this->envVar($dbnameVar);

        // Build the PDO Data Source Name (DSN) string for MySQL connection
        return "mysql:host={$this->host};dbname={$this->dbname}";
    }

    private function hasValidCredentials(): bool
    {
        // Check whether all required connection credentials are present
        return !empty($this->host) && !empty($this->dbname) && !empty($this->user) && !empty($this->password);
    }

    private function setDb(string $dbnameVar)
    {
        // Build the DSN and populate $this->host / $this->dbname along the way
        $dsn = $this->buildDsn($dbnameVar);

        // Load remaining credentials from environment variables
        $this->user     = $this->envVar('TOOL_TOOLSDB_USER');
        $this->password = $this->envVar('TOOL_TOOLSDB_PASSWORD');

        // If any required credential is missing, skip the connection attempt entirely
        // instead of letting PDO fail with a connection error
        if (!$this->hasValidCredentials()) {
            $this->db = null;
            error_log('Database credentials are not fully configured; skipping DB connection.');
            Logger::debug('Database credentials are not fully configured; skipping DB connection.');
            return;
        }

        try {
            // Attempt to establish the database connection
            $this->db = new PDO($dsn, $this->user, $this->password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->db = null;
            Logger::debug($e->getMessage());
            // Log the error message
            error_log($e->getMessage());
            if ($this->appEnv === 'testing') {
                return;
            }
            // Display a generic message
            echo "Unable to connect to the database. Please try again later.";
            throw new \RuntimeException('Database connection failed');
        }
    }
    public function disableFullGroupByMode(string $sqlQuery): void
    {
        if ($this->db === null) {
            return;
        }

        // if the query contains "GROUP BY", disable ONLY_FULL_GROUP_BY, strtoupper() is for case insensitive
        if (strpos(strtoupper($sqlQuery), 'GROUP BY') !== false && !$this->groupByModeDisabled) {
            try {
                // More precise SQL mode modification
                $this->db->exec("SET SESSION sql_mode=(SELECT REPLACE(@@SESSION.sql_mode,'ONLY_FULL_GROUP_BY',''))");
                $this->groupByModeDisabled = true;
            } catch (PDOException $e) {
                // Log error but don't fail the query
                error_log("Failed to disable ONLY_FULL_GROUP_BY: " . $e->getMessage());
            }
        }
    }

    public function fetchquery(string $sqlQuery, ?array $params = null): array
    {
        if ($this->db === null) {
            error_log("Database connection is not established.");
            return [];
        };
        Logger::debug("fetchquery: | Query: " . $sqlQuery);

        try {
            $this->disableFullGroupByMode($sqlQuery);

            $q = $this->db->prepare($sqlQuery);
            if ($params) {
                $q->execute($params);
            } else {
                $q->execute();
            }

            // Fetch the results if it's a SELECT query
            $result = $q->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log("SQL Error in fetchquery: " . $e->getMessage() . " | Query: " . $sqlQuery);
            Logger::debug("SQL Error in fetchquery: " . $e->getMessage() . " | Query: " . $sqlQuery);
            // In testing mode, re-throw to allow tests to skip
            if ($this->appEnv === 'testing') {
                throw $e;
            }
            return [];
        }
    }
    public function executequery(string $sqlQuery, ?array $params = null): bool
    {
        if ($this->db === null) {
            error_log("Database connection is not established.");
            return false;
        };
        Logger::debug("executequery: | Query: " . $sqlQuery);

        try {
            $this->disableFullGroupByMode($sqlQuery);

            $q = $this->db->prepare($sqlQuery);
            if ($params) {
                $q->execute($params);
            } else {
                $q->execute();
            }
            error_log("Rows affected: " . $q->rowCount());
            return true;
        } catch (PDOException $e) {
            error_log("SQL Error in executequery: " . $e->getMessage() . " | Query: " . $sqlQuery);
            Logger::debug("SQL Error in executequery: " . $e->getMessage() . " | Query: " . $sqlQuery);
            // In testing mode, re-throw to allow tests to skip
            if ($this->appEnv === 'testing') {
                throw $e;
            }
            return false;
        }
    }

    // ------------------------------------------------------------------
    // Transactions
    // ------------------------------------------------------------------

    /**
     * Starts a PDO transaction. Returns false (and logs) if there is no
     * live connection or a transaction is already active, instead of
     * letting PDO throw.
     */
    public function beginTransaction(): bool
    {
        if ($this->db === null) {
            error_log("Database connection is not established.");
            return false;
        }

        if ($this->db->inTransaction()) {
            return true;
        }

        try {
            return $this->db->beginTransaction();
        } catch (PDOException $e) {
            error_log("SQL Error in beginTransaction: " . $e->getMessage());
            if ($this->appEnv === 'testing') {
                throw $e;
            }
            return false;
        }
    }

    public function commit(): bool
    {
        if ($this->db === null || !$this->db->inTransaction()) {
            return false;
        }

        try {
            return $this->db->commit();
        } catch (PDOException $e) {
            error_log("SQL Error in commit: " . $e->getMessage());
            if ($this->appEnv === 'testing') {
                throw $e;
            }
            return false;
        }
    }

    public function rollback(): bool
    {
        if ($this->db === null || !$this->db->inTransaction()) {
            return false;
        }

        try {
            return $this->db->rollBack();
        } catch (PDOException $e) {
            error_log("SQL Error in rollback: " . $e->getMessage());
            if ($this->appEnv === 'testing') {
                throw $e;
            }
            return false;
        }
    }

    public function inTransaction(): bool
    {
        return $this->db !== null && $this->db->inTransaction();
    }

    public function __destruct()
    {
        $this->db = null;
    }
}
