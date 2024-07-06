<?PHP
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

include_once 'header.php';
echo '<script>$("#leaderboard").addClass("active");</script>';
include_once 'Tables/langcode.php';
include_once 'Tables/tables.php';
include_once 'actions/functions.php';

include_once 'leaderboard/graph.php';
include_once 'Tables/sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat

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
include_once 'foter.php';
?>
