<?php

namespace APICalls\MdwikiSql;
/*
Usage:
use function APICalls\MdwikiSql\fetch_query;
use function APICalls\MdwikiSql\execute_query;
*/

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
use PDO;
use PDOException;

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
        $this->host = $this->envVar('DB_HOST') ?: 'tools.db.svc.wikimedia.cloud';
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
            exit();
        }
    }

    public function test_print($s)
    {
        if (isset($_COOKIE['test']) && $_COOKIE['test'] == 'x') {
            return;
        }

        $print_t = (isset($_REQUEST['test']) || isset($_COOKIE['test'])) ? true : false;

        if ($print_t && gettype($s) == 'string') {
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
            $this->test_print($sql_query);

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

function get_dbname($table_name)
{
    // Load from configuration file or define as class constant
    $table_db_mapping = [
        'DB_NAME_NEW' => [
            "missing",
            "missing_by_qids",
            "exists_by_qids",
            "publish_reports",
            "login_attempts",
            "logins",
            "publish_reports_stats",
            "all_qids_titles"
        ],
        'DB_NAME' => [] // default
    ];

    if ($table_name) {
        foreach ($table_db_mapping as $db => $tables) {
            if (in_array($table_name, $tables)) {
                return $db;
            }
        }
    }

    return 'DB_NAME'; // default
}

function execute_query(string $sql_query, $params = null, $table_name = null)
{

    $dbname_var = get_dbname($table_name);

    // Create a new database object
    $db = new Database($dbname_var);

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

    //---
    return $results;
};
function fetch_query(string $sql_query, $params = null, $table_name = null)
{

    $dbname_var = get_dbname($table_name);

    // Create a new database object
    $db = new Database($dbname_var);

    // Execute a SQL query
    if ($params) {
        $results = $db->fetchquery($sql_query, $params);
    } else {
        $results = $db->fetchquery($sql_query);
    }

    // Print the results
    // foreach ($results as $row) echo $row['column1'] . " " . $row['column2'] . "<br>";

    // Destroy the database object
    $db = null;

    //---
    return $results;
};
