<?php
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    $fa = $_GET['test'] ?? '';
    if ($fa != 'xx') {
        // Get the Request Token's details from the session and create a new Token object.
        session_start();
        // ---
        $user = 'Mr. Ibrahem';
        $_SESSION['username'] = $user;
        //---
        // require_once __DIR__ . "/../actions/mdwiki_sql.php";
        // sql_add_user($user, '', '', '', '');
        //---
        header("Location: /Translation_Dashboard/index.php");
        exit(0);
    };
};
//---
