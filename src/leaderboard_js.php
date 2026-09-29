<?PHP

use App\Templates\PageHeader;
use App\Templates\PageFooter;
use App\User\CurrentUser;
use App\Leaderboard\IndexJsLeaderboard;

include_once __DIR__ . '/app/include_all.php';
include_once __DIR__ . '/templates/include.php';

$currentUser = CurrentUser::getInstance();

$pageHeader = new PageHeader($currentUser);
$pageHeader->render();

(new IndexJsLeaderboard())->render();

$timeStart = $pageHeader->getLoadStartTime();

$pageFooter = new PageFooter($currentUser);
$pageFooter->render($timeStart);
