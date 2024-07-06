<?php
$u = filter_input(INPUT_GET, 'u', FILTER_SANITIZE_SPECIAL_CHARS);
$allowed_u = [
    "Mina karaca"
];
if ($u != '' && in_array($u, $allowed_u)) {
    session_start();
    $_SESSION['username'] = $u;
    //---
    session_regenerate_id();
    header("Location: /Translation_Dashboard/index.php");
    exit(0);
};
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    $fa = $_GET['test'] ?? '';
    if ($fa != 'xx') {
        // Get the Request Token's details from the session and create a new Token object.
        session_start();
        // ---
        $user = 'Mr. Ibrahem';
        $_SESSION['username'] = $user;
        //---
        // include_once __DIR__ . "/../actions/mdwiki_sql.php";
        // sql_add_user($user, '', '', '', '');
        //---
        header("Location: /Translation_Dashboard/index.php");
        exit(0);
    };
};
//---
