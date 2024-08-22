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
    <div class='card'>
        <div class='card-body' style='padding:5px 0px 5px 5px;'>
HTML;
// ---
require_once __DIR__ . '/main_get.php';
// ---
echo start_main_get();
// ---
echo <<<HTML
        </div>
    </div>
</div>
HTML;
//---
include_once __DIR__ . '/../foter.php';
//---
