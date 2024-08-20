<?php
include_once __DIR__ . '/../vendor_load.php';

$configFile = __DIR__ . '/config.php';

if (!file_exists($configFile)) {
	echo "Configuration could not be read. Please create $configFile by copying config.dist.php";
	exit(1);
}

include_once $configFile;
include_once __DIR__ . '/helps.php';
//---
$allowedActions = ['login', 'callback', 'logout', 'edit'];
$action = $_GET['a'] ?? 'user_infos';
//---
if (in_array($action, $allowedActions)) {

	$actionFile = $action . '.php';

	// Redirect to the corresponding action file
	// header("Location: " . $actionFile);
	include_once __DIR__ . "/" . $actionFile;
};
