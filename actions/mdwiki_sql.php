<?php
//---
function sql_localhost($quae) {
    //---
    $host = 'localhost:3306';
    $dbname = "mdwiki";
    //---
    try {
        // إجراء الإتصال
        $db = new PDO(
                "mysql:host=$host;dbname=$dbname", 
                'root', 
                'root11'
                );
        //---
        $q = $db->prepare($quae);
        $q->execute();
        //---
        $result = $q->fetchAll(PDO::FETCH_ASSOC);
        //---
        return $result;
    } 
    catch(PDOException $e) {
        echo $quae . "<br>" . $e->getMessage();
        return array();
    }
    //---
    // إغلاق الإتصال
    $db = null;
    //---
}
//---
function sql_tools_db($quae) {
    //---
    $ts_pw = posix_getpwuid(posix_getuid()); 
    $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/replica.my.cnf");
    //---
    $host = 'tools.db.svc.wikimedia.cloud';
    $dbname = $ts_mycnf['user'] . "__mdwiki";
    //---
    try {
        // إجراء الإتصال
        $db = new PDO(
            "mysql:host=$host;dbname=$dbname", 
            $ts_mycnf['user'], 
            $ts_mycnf['password']
            );
        //---
        unset($ts_mycnf, $ts_pw);
        //---
        $q = $db->prepare($quae);
        //---
        $q->execute();
        $result = $q->fetchAll(PDO::FETCH_ASSOC);
        return $result;
        //---
    } 
    catch(PDOException $e) {
        echo $quae . "<br>" . $e->getMessage();
		return array();
    };
    //---
    $db = null;
    //---
}
//---
function execute_query($sql_query) {
    if ($_SERVER['SERVER_NAME'] === 'localhost') {
        return sql_localhost($sql_query);
    } else {
        return sql_tools_db($sql_query);
    }
};
//---
function execute_query_2($quae) {
    return execute_query($quae);    
}
//---
function query2($quae) {
    //---
    $sql_u = execute_query($quae);
    //---
    $sql_result = array();
    //---
    $n = 0;
    //---
    foreach ( $sql_u AS $id => $row ) {
        $ff = array();
        $n = $n + 1 ;
        foreach ( $row AS $nas => $value ) {
            $ff[$nas] = $value;
        };
        $sql_result[$n] = $ff;
    };
    //---
    unset($sql_u);
    //---
    return $sql_result;
}; 
//---
?>