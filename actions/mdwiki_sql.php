<?php
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
    
    public function execute_query($sql_query) {
        try {
            $q = $this->db->prepare($sql_query);
            $q->execute();
            $result = $q->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch(PDOException $e) {
            echo $sql_query . "<br>" . $e->getMessage();
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
