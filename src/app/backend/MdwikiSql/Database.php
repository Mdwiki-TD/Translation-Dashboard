<?php

/**
 * Database Abstraction Layer for MDWiki SQL Operations
 *
 */

namespace App\MdwikiSql;

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
    private $groupByModeDisabled = false;

    public function __construct(string $dbnameVar = 'DB_NAME')
    {
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
    private function setDb(string $dbnameVar)
    {
        $this->host = $this->envVar('DB_HOST_TOOLS') ?: 'tools.db.svc.wikimedia.cloud';
        $this->dbname = $this->envVar($dbnameVar);
        $this->user = $this->envVar('TOOL_TOOLSDB_USER');
        $this->password = $this->envVar('TOOL_TOOLSDB_PASSWORD');

        try {
            $this->db = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Log the error message
            error_log($e->getMessage());
            // Display a generic message
            echo "Unable to connect to the database. Please try again later.";
            throw new \RuntimeException('Database connection failed');
            // exit();
        }
    }

    public function test_print($s)
    {
        if (isset($_COOKIE['test']) && $_COOKIE['test'] == 'x') {
            return;
        }

        $print_t = (isset($_REQUEST['test']) || isset($_COOKIE['test'])) ? true : false;

        if ($print_t && is_string($s)) {
            echo "\n<br>\n$s";
        } elseif ($print_t) {
            echo "\n<br>\n";
            print_r($s);
        }
    }

    public function disableFullGroupByMode(string $sqlQuery)
    {
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

    public function fetchquery(string $sqlQuery, $params = null)
    {
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
            echo "SQL Error:" . $e->getMessage() . "<br>" . $sqlQuery;
            error_log("SQL Error in fetchquery: " . $e->getMessage() . " | Query: " . $sqlQuery);
            return [];
        }
    }

    public function executequery(string $sqlQuery, $params = null)
    {
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
            echo "sql error:" . $e->getMessage() . "<br>" . $sqlQuery;
            error_log("SQL Error in executequery: " . $e->getMessage() . " | Query: " . $sqlQuery);
            $this->test_print("SQL Error in executequery: " . $e->getMessage() . " | Query: " . $sqlQuery);
            return false;
        }
    }

    public function executQqueryOrFail(string $sqlQuery, $params = null): void
    {
        $this->disableFullGroupByMode($sqlQuery);

        $q = $this->db->prepare($sqlQuery);
        if ($params) {
            $q->execute($params);
        } else {
            $q->execute();
        }
        error_log("Rows affected: " . $q->rowCount());
    }

    public function __destruct()
    {
        $this->db = null;
    }
}
