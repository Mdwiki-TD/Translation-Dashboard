<?php
// this file is redirected to files in the auth directory
// example:
// url auth.php?a=login  -> auth/login.php
// url auth.php?a=logout -> auth/logout.php
// url auth.php?a=edit   -> auth/edit.php
// code:

// After
$allowedActions = ['login', 'callback', 'logout', 'edit', 'api', 'index'];

$action = $_GET['a'] ?? 'index';

if (!in_array($action, $allowedActions)) {
    // Handle error or redirect to a default action
    $action = 'index';
}
$actionFile = $action . '.php';

// Redirect to the corresponding action file
// header("Location: auth/" . $actionFile);
include_once __DIR__ . "/auth/" . $actionFile;

if ($action == 'index') {
    echo_login();
}
exit;
