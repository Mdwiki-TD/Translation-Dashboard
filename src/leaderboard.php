<?PHP

use App\Templates\PageHeader;
use App\Templates\PageFooter;
use App\User\CurrentUser;

include_once __DIR__ . '/app/include_all.php';
include_once __DIR__ . '/templates/include.php';

$currentUser = CurrentUser::getInstance();

$pageHeader = new PageHeader($currentUser);
$pageHeader->render();

include_once __DIR__ . '/app/leaderboard/main.php';

include_once __DIR__ . '/app/leaderboard/index.php';

$timeStart = $pageHeader->getLoadStartTime();

$pageFooter = new PageFooter($currentUser);
$pageFooter->render($timeStart);
