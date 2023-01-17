<?PHP
//---
if ($_GET['test'] != '') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
require('header.php');
require('langcode.php');
require('tables.php');
include_once('functions.php');
//---
$users  = isset($_REQUEST['user']) ? $_REQUEST['user'] : '';
$langs  = isset($_REQUEST['langcode']) ? $_REQUEST['langcode'] : '';
//---
if ($users != '') {
    require('leaderboard/users.php');
} elseif ($langs != '') {
    require('leaderboard/langs.php');
} else {
    require('leader_tables.php');
	require('leaderboard/index.php');
};
//---
require('foter.php');
//---
?>