<?php
// this file is redirected to files in the auth directory
// example:
// url auth.php?a=login  -> auth/login.php
// url auth.php?a=logout -> auth/logout.php
// url auth.php?a=edit   -> auth/edit.php
// code:
// Extract the action parameter from the URL
$action = $_GET['a'] ?? 'index';

// Determine the corresponding action file
switch ($action) {
    case 'login':
        $actionFile = 'login.php';
        break;
    case 'callback':
        $actionFile = 'callback.php';
        break;
    case 'logout':
        $actionFile = 'logout.php';
        break;
    case 'edit':
        $actionFile = 'edit.php';
        break;
    case 'api':
        $actionFile = 'api.php';
        break;
    default:
        $actionFile = 'index.php';
}

// Redirect to the corresponding action file
// header("Location: auth/" . $actionFile);
require_once __DIR__ . "/auth/" . $actionFile;
if ($action == 'index') {
    echo_login();
}
exit;
