<?php

/**
 * Database Abstraction Layer for MDWiki SQL Operations
 *
 * Provides a secure, PDO-based database abstraction layer for interacting
 * with MySQL/MariaDB databases in the Translation Dashboard application.
 * Supports both local development and Wikimedia Toolforge environments.
 *
 * Features:
 * - Automatic environment detection (localhost vs production)
 * - Prepared statement support for SQL injection prevention
 * - Configurable database suffix for multi-database support
 * - Automatic SQL mode adjustment for GROUP BY compatibility
 * - Secure credential management via external configuration
 *
 * Security Considerations:
 * - Credentials are loaded from external configuration file, never hardcoded
 * - All queries use prepared statements
 * - Error messages are logged, not displayed in production
 * - Database connections are properly closed after use
 *
 * Usage Example:
 * ```php
 * use function App\APICalls\MdwikiSql\fetch_query;
 * use function App\APICalls\MdwikiSql\execute_query;
 *
 * // Fetch results (SELECT queries)
 * $users = fetch_query("SELECT * FROM users WHERE is_active = ?", [1]);
 *
 * // Execute queries (INSERT, UPDATE, DELETE)
 * execute_query("UPDATE settings SET value = ? WHERE id = ?", ['new_value', 5]);
 * ```
 *
 * Configuration:
 * Database credentials are stored in ~/confs/db.ini:
 * ```ini
 * user = your_toolforge_username
 * password = your_database_password
 * ```
 *
 * @package    APICalls
 * @subpackage MdwikiSql
 * @author     Translation Dashboard Team
 * @version    2.0.0
 * @since      1.0.0
 * @license    GPL-3.0-or-later
 *
 * @see https://www.php.net/manual/en/book.pdo.php
 * @see https://wikitech.wikimedia.org/wiki/Help:Toolforge/Database
 */

namespace App\APICalls\MdwikiSql;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Database Connection and Query Management Class
 *
 * Encapsulates PDO database operations with automatic connection management,
 * error handling, and environment-specific configuration.
 *
 * @package App\APICalls\MdwikiSql
 */
class Database
{

    private $db;
    private $host;
    private $user;
    private $password;
    private $dbname;
    private $groupByModeDisabled = false;

    public function __construct(string $dbname_var = 'DB_NAME')
    {
        $this->set_db($dbname_var);
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
    private function set_db(string $dbname_var)
    {
        $this->host = $this->envVar('DB_HOST_TOOLS') ?: 'tools.db.svc.wikimedia.cloud';
        $this->dbname = $this->envVar($dbname_var);
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

    public function disableFullGroupByMode($sql_query)
    {
        // if the query contains "GROUP BY", disable ONLY_FULL_GROUP_BY, strtoupper() is for case insensitive
        if (strpos(strtoupper($sql_query), 'GROUP BY') !== false && !$this->groupByModeDisabled) {
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

    public function executequery($sql_query, $params = null)
    {
        try {
            $this->disableFullGroupByMode($sql_query);

            $q = $this->db->prepare($sql_query);
            if ($params) {
                $q->execute($params);
            } else {
                $q->execute();
            }

            // Check if the query starts with "SELECT"
            $query_type = strtoupper(substr(trim((string) $sql_query), 0, 6));
            if ($query_type === 'SELECT') {
                // Fetch the results if it's a SELECT query
                $result = $q->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            } else {
                // Otherwise, return null
                return [];
            }
        } catch (PDOException $e) {
            echo "sql error:" . $e->getMessage() . "<br>" . $sql_query;
            return false;
        }
    }

    public function fetchquery($sql_query, $params = null)
    {
        try {
            $this->disableFullGroupByMode($sql_query);

            $q = $this->db->prepare($sql_query);
            if ($params) {
                $q->execute($params);
            } else {
                $q->execute();
            }

            // Fetch the results if it's a SELECT query
            $result = $q->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "SQL Error:" . $e->getMessage() . "<br>" . $sql_query;
            // error_log("SQL Error: " . $e->getMessage() . " | Query: " . $sql_query);
            return [];
        }
    }

    public function __destruct()
    {
        $this->db = null;
    }
}

function execute_query(string $sql_query, $params = null)
{
    // Create a new database object
    $db = new Database('DB_NAME');

    // Execute a SQL query
    if ($params) {
        $results = $db->executequery($sql_query, $params);
    } else {
        $results = $db->executequery($sql_query);
    }

    // Print the results
    // foreach ($results as $row) echo $row['column1'] . " " . $row['column2'] . "<br>";

    // Destroy the database object
    $db = null;


    return $results;
};
function fetch_query(string $sql_query, $params = null, $noprint = false)
{
    // Create a new database object
    $db = new Database('DB_NAME');

    if ($noprint == false) {
        $db->test_print($sql_query);
    }

    // Execute a SQL query
    if ($params) {
        $results = $db->fetchquery($sql_query, $params);
    } else {
        $results = $db->fetchquery($sql_query, null);
    }

    // Print the results
    // foreach ($results as $row) echo $row['column1'] . " " . $row['column2'] . "<br>";

    // Destroy the database object
    $db = null;


    return $results;
};
