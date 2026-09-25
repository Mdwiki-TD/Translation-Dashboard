<?PHP
http_response_code(404);

include_once __DIR__ . '/app/include_all.php';

use App\Templates\PageHeader;
use App\Templates\PageFooter;
use App\User\CurrentUser;

include_once __DIR__ . '/templates/include.php';

$currentUser = CurrentUser::getInstance();

$pageHeader = new PageHeader($currentUser);
$pageHeader->render();


function print_h3_title($h3_title)
{
	echo <<<HTML
    <div class="card-header aligncenter" style="font-weight:bold;">
        <h3>$h3_title</h3>
    </div>
    <div class="card-body">
HTML;
}

print_h3_title("404 Error.");

echo <<<HTML
<div class="wrapper">
	<div class="header">
		<p>The page you requested was not found.</p>
	</div>
</div>
HTML;


$timeStart = $pageHeader->getLoadStartTime();

$pageFooter = new PageFooter($currentUser);
$pageFooter->render($timeStart);
