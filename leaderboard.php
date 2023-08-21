<?PHP
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

require 'header.php';
echo '<script>$("#leaderboard").addClass("active");</script>';
require 'langcode.php';
require 'tables.php';
include_once 'functions.php';

require 'leaderboard/graph.php';
include_once 'sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat

$users = $_REQUEST['user'] ?? '';
$langs = $_REQUEST['langcode'] ?? '';
$graph = $_REQUEST['graph'] ?? '';
$camps = $_REQUEST['camps'] ?? '';
if ($users !== '') {
    require 'leaderboard/users.php';
} elseif ($langs !== '') {
    require 'leaderboard/langs.php';
} elseif ($camps !== '') {
    require 'leaderboard/camps.php';
    camps_list();
} elseif ($graph !== '') {
    print_graph_tab();
} else {
    require 'leaderboard/index.php';
}
?>

<!-- <script src="/Translation_Dashboard/js/leadtable.js"></script> -->

<?php
require 'foter.php';
?>
