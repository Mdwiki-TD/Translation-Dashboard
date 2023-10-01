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
    private $user;
    private $password;
    private $dbname;
    
    public function __construct($server_name) {
        if ($server_name === 'localhost') {
            $this->host = 'localhost:3306';
            $this->dbname = 'mdwiki';
            $this->user = 'root';
            $this->password = 'root11';
        } else {
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/replica.my.cnf");
            $this->host = 'tools.db.svc.wikimedia.cloud';
            $this->dbname = $ts_mycnf['user'] . "__mdwiki";
            $this->user = $ts_mycnf['user'];
            $this->password = $ts_mycnf['password'];
            unset($ts_mycnf, $ts_pw);
        }
        
        try {
            $this->db = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
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
function update_settings($id, $title, $displayed, $value, $type) {
    $query = <<<SQL
        UPDATE settings SET title = '$title', displayed = '$displayed', Type = '$type', value = '$value' WHERE id = '$id'
    SQL;
    //---
    if ($id == 0 || $id == '0' || $id == '') {
        $query = "INSERT INTO settings (id, title, displayed, Type, value) SELECT '$id', '$title', '$displayed', '$type', '$value' WHERE NOT EXISTS (SELECT 1 FROM settings WHERE title = '$title')";
    };
    //---
    $result = execute_query($query);
    //---
    return $result;
}
//---
function insert_to_projects($g_title, $g_id) {
    $query = "UPDATE projects SET g_title = '$g_title' WHERE g_id = '$g_id'";
    //---
    if ($g_id == 0 || $g_id == '0' || $g_id == '') {
        $query = "INSERT INTO projects (g_title) SELECT '$g_title' WHERE NOT EXISTS (SELECT 1 FROM projects WHERE g_title = '$g_title')";
    };
    //---
    $result = execute_query($query);
    //---
    return $result;
}
function get_all($tab="(categories|coordinator|copy_pages|pages|projects|qids|users|views|wddone|words)") {
    //---
    $query = "SELECT * FROM $tab";
    $data = execute_query($query);
    //---
    return $data;
}
//---
function display_tables() {
    $sql_query = "SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME";
    $result = execute_query($sql_query);
    //---
    $tables = [];
    foreach ($result as $row) $tables[] = $row['TABLE_NAME'];
    //---
    // test_print($tables);
}
//---
$test = $_GET['test'] ?? '';
if ($test != '') display_tables();
?>
