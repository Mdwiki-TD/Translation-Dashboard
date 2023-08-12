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

include_once 'sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat

$users = $_REQUEST['user'] ?? '';
$langs = $_REQUEST['langcode'] ?? '';
$graph = $_REQUEST['graph'] ?? '';
require 'leaderboard/graph.php';
if ($users !== '') {
    require 'leaderboard/users.php';
} elseif ($langs !== '') {
    require 'leaderboard/langs.php';
} elseif ($graph !== '') {
    $g = print_graph();
    echo <<<HTML
        <div class="container">
            $g
        </div>
    HTML;
} else {
    require 'leaderboard/index.php';
}

require 'foter.php';
?>