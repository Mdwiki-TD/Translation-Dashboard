<?PHP
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

include_once __DIR__ . '/header.php';
include_once __DIR__ . '/actions/functions.php';
include_once __DIR__ . '/Tables/langcode.php';
include_once __DIR__ . '/Tables/tables.php';
include_once __DIR__ . '/Tables/sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat

require __DIR__ . '/leaderboard/index.php';

echo '<!-- <script src="/Translation_Dashboard/js/leadtable.js"></script> -->';

include_once __DIR__ . '/footer.php';
?>
