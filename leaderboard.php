<?PHP
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

include_once __DIR__ . '/header.php';
include_once __DIR__ . '/actions/load_request.php';
include_once __DIR__ . '/Tables/include.php';

include_once __DIR__ . '/leaderboard/index.php';

// use function Actions\TestPrint\test_print;

echo '<!-- <script src="/Translation_Dashboard/js/leadtable.js"></script> -->';

include_once __DIR__ . '/footer.php';
