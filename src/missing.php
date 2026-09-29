<?PHP


use App\Layout\PageHeader;
use App\Layout\PageFooter;
use App\User\CurrentUser;
use App\Missing\MissingController;

include_once __DIR__ . '/app/include_all.php';
include_once __DIR__ . '/Layout/include.php';

$currentUser = CurrentUser::getInstance();

$pageHeader = new PageHeader($currentUser);
$pageHeader->render();

(new MissingController())->handleRequest();

$timeStart = $pageHeader->getLoadStartTime();

$pageFooter = new PageFooter($currentUser);
$pageFooter->render($timeStart);
