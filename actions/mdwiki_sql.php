<?php
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
class Database {
    
    private $db;
    private $host;
    private $username;
    private $password;
    private $dbname;
    
    public function __construct($server_name) {
        if ($server_name === 'localhost') {
            $this->host = 'localhost:3306';
            $this->dbname = 'mdwiki';
            $this->username = 'root';
            $this->password = 'root11';
        } else {
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/replica.my.cnf");
            $this->host = 'tools.db.svc.wikimedia.cloud';
            $this->dbname = $ts_mycnf['user'] . "__mdwiki";
            $this->username = $ts_mycnf['user'];
            $this->password = $ts_mycnf['password'];
            unset($ts_mycnf, $ts_pw);
        }
        
        try {
            $this->db = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }
    
    public function execute_query_old($sql_query) {
        try {
            $q = $this->db->prepare($sql_query);
            $q->execute();
            $result = $q->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch(PDOException $e) {
            echo "sql error:" . $e->getMessage() . "<br>" . $sql_query;
            return array();
        }
    }
    public function execute_query($sql_query) {
        try {
            $q = $this->db->prepare($sql_query);
            $q->execute();
            
            // Check if the query starts with "SELECT"
            $query_type = strtoupper(substr(trim((string) $sql_query), 0, 6));
            if ($query_type === 'SELECT') {
                // Fetch the results if it's a SELECT query
                $result = $q->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            } else {
                // Otherwise, return null
                return array();
            }
        } catch(PDOException $e) {
            echo "sql error:" . $e->getMessage() . "<br>" . $sql_query;
            return array();
        }
    }
    
    public function __destruct() {
        $this->db = null;
    }
}
//---
function execute_query($sql_query) {
        
    // Create a new database object
    $db = new Database($_SERVER['SERVER_NAME']);

    // Execute a SQL query
    $results = $db->execute_query($sql_query);

    // Print the results
    // foreach ($results as $row) echo $row['column1'] . " " . $row['column2'] . "<br>";

    // Destroy the database object
    $db = null;

    //---
    return $results;
};
//---
?>
