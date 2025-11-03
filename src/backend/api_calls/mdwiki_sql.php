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
use RuntimeException;

class Database
{

    private $db;
    private $host;
    private $home_dir;
    private $user;
    private $password;
    private $dbname;
    private $db_suffix;
    private $groupByModeDisabled = false;

    public function __construct($server_name, $db_suffix = 'mdwiki')
    {
        if (empty($db_suffix)) {
            $db_suffix = 'mdwiki';
        }
        // ---
        $this->home_dir = getenv("HOME") ?: 'I:/mdwiki/mdwiki';
        //---
        $this->db_suffix = $db_suffix;
        $this->set_db($server_name);
    }

    private function set_db($server_name)
    {
        // $ts_pw = posix_getpwuid(posix_getuid());
        // $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/confs/db.ini");
        // ---
        $config_path = $this->home_dir . "/confs/db.ini";
        $ts_mycnf = @parse_ini_file($config_path);
        if ($ts_mycnf === false) {
            throw new RuntimeException(sprintf('Database configuration file "%s" could not be read or parsed.', $config_path));
        }

        $required_keys = ['user'];
        if ($server_name !== 'localhost') {
            $required_keys[] = 'password';
        }

        foreach ($required_keys as $key) {
            if (!array_key_exists($key, $ts_mycnf) || $ts_mycnf[$key] === '') {
                throw new RuntimeException(sprintf('Database configuration is missing required "%s" entry in "%s".', $key, $config_path));
            }
        }
        // ---
        if ($server_name === 'localhost') {
            $this->host = 'localhost:3306';
            $this->dbname = $ts_mycnf['user'] . "__" . $this->db_suffix;
            $this->user = 'root';
            $this->password = 'root11';
        } else {
            $this->host = 'tools.db.svc.wikimedia.cloud';
            $this->dbname = $ts_mycnf['user'] . "__" . $this->db_suffix;
            $this->user = $ts_mycnf['user'];
            $this->password = $ts_mycnf['password'];
        }
        // unset($ts_mycnf, $ts_pw);
        unset($ts_mycnf);

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
        'mdwiki_new' => [
            "missing",
            "missing_by_qids",
            "exists_by_qids",
            "publish_reports",
            "login_attempts",
            "logins",
            "publish_reports_stats",
            "all_qids_titles"
        ],
        'mdwiki' => [] // default
    ];

    if ($table_name) {
        foreach ($table_db_mapping as $db => $tables) {
            if (in_array($table_name, $tables)) {
                return $db;
            }
        }
    }

    return 'mdwiki'; // default
}

function execute_query($sql_query, $params = null, $table_name = null)
{

    $dbname = get_dbname($table_name);

    // Create a new database object
    $db = new Database($_SERVER['SERVER_NAME'] ?? '', $dbname);

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
function fetch_query($sql_query, $params = null, $table_name = null)
{

    $dbname = get_dbname($table_name);

    // Create a new database object
    $db = new Database($_SERVER['SERVER_NAME'] ?? '', $dbname);

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
