<?PHP

include_once __DIR__ . '/app/include_all.php';

use App\Templates\PageHeader;
use App\Templates\PageFooter;
use App\User\CurrentUser;

include_once __DIR__ . '/templates/PageHead.php';
include_once __DIR__ . '/templates/PageHeader.php';
include_once __DIR__ . '/templates/PageFooter.php';

$currentUser = CurrentUser::getInstance();

$pageHeader = new PageHeader($currentUser);
$pageHeader->render();

include_once __DIR__ . '/app/missing.php';

$timeStart = $pageHeader->getLoadStartTime();

$pageFooter = new PageFooter($currentUser);
$pageFooter->render($timeStart);
