<?PHP
//---
include_once __DIR__ . '/../header.php';
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
echo <<<HTML
        <div align=center>
        <h4>Top languages by missing Articles ($date)</h4>
        <h5>Number of pages in Category:RTT : $lenth</h5>
    </div>
    <div class='card'>
        <div class='card-body' style='padding:5px 0px 5px 5px;'>
HTML;
// ---
require_once __DIR__ . '/main.php';
// ---
echo <<<HTML
        </div>
    </div>
</div>
HTML;
//---
include_once __DIR__ . '/../foter.php';
//---
