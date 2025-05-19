<?php

namespace Actions\MdwikiSql;
/*
Usage:
use function Actions\MdwikiSql\fetch_query;
use function Actions\MdwikiSql\execute_query;
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

    public function __construct($server_name)
    {
        if ($server_name === 'localhost' || !getenv('HOME')) {
            $this->host = 'localhost:3306';
            $this->dbname = 'mdwiki';
            $this->user = 'root';
            $this->password = 'root11';
        } else {
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/confs/db.ini");
            $this->host = 'tools.db.svc.wikimedia.cloud';
            $this->dbname = $ts_mycnf['db'];
            $this->user = $ts_mycnf['user'];
            $this->password = $ts_mycnf['password'];
            unset($ts_mycnf, $ts_pw);
        }

        try {
            $this->db = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
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

    public function disableFullGroupByMode()
    {
        // إزالة ONLY_FULL_GROUP_BY مرة واحدة لكل جلسة
        $this->db->exec("SET SESSION sql_mode=(SELECT REPLACE(@@SESSION.sql_mode,'ONLY_FULL_GROUP_BY',''))");
    }

    public function execute_query($sql_query, $params = null)
    {
        try {
            $this->disableFullGroupByMode();

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
            return [];
        }
    }

    public function fetch_query($sql_query, $params = null)
    {
        try {
            // $this->test_print($sql_query);

            $this->disableFullGroupByMode();

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
            echo "sql error:" . $e->getMessage() . "<br>" . $sql_query;
            return [];
        }
    }

    public function __destruct()
    {
        $this->db = null;
    }
}

function execute_query($sql_query, $params = null)
{

    // Create a new database object
    $db = new Database($_SERVER['SERVER_NAME'] ?? '');

    // Execute a SQL query
    if ($params) {
        $results = $db->execute_query($sql_query, $params);
    } else {
        $results = $db->execute_query($sql_query);
    }

    // Print the results
    // foreach ($results as $row) echo $row['column1'] . " " . $row['column2'] . "<br>";

    // Destroy the database object
    $db = null;

    //---
    return $results;
};
function fetch_query($sql_query, $params = null)
{

    // Create a new database object
    $db = new Database($_SERVER['SERVER_NAME'] ?? '');

    // Execute a SQL query
    if ($params) {
        $results = $db->fetch_query($sql_query, $params);
    } else {
        $results = $db->fetch_query($sql_query);
    }

    // Print the results
    // foreach ($results as $row) echo $row['column1'] . " " . $row['column2'] . "<br>";

    // Destroy the database object
    $db = null;

    //---
    return $results;
};
