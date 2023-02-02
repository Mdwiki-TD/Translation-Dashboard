<?PHP
//---
if (isset($_REQUEST['test'])) {
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
include_once('sql_tables.php'); // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
//---
$users  = isset($_REQUEST['user']) ? $_REQUEST['user'] : '';
$langs  = isset($_REQUEST['langcode']) ? $_REQUEST['langcode'] : '';
//---
if ($users != '') {
    require('leaderboard/users.php');
} elseif ($langs != '') {
    require('leaderboard/langs.php');
} else {
	require('leaderboard/index.php');
};
//---
require('foter.php');
//---
?>