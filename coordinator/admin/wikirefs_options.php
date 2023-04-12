<?php
//---
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
include_once('td_config.php');
//---
$tabes = get_configs('fixwikirefs.json');
//---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require('wikirefs_options/post.php');
}
//---
require('wikirefs_options/load.php');
//---
?>