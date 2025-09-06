<?PHP
//---
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
include_once __DIR__ . '/include_all.php';
include_once __DIR__ . '/header.php';
//---
include_once __DIR__ . '/leaderboard/main.php';
//---
include_once __DIR__ . '/leaderboard/index.php';
//---
echo '<!-- <script src="/Translation_Dashboard/js/leadtable.js"></script> -->';

include_once __DIR__ . '/footer.php';
