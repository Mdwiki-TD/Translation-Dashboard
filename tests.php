<?PHP
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
require 'header.php';
//---
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
echo '<script>$("#tests").addClass("active");</script>';
//---
include_once('functions.php');
include_once('sql_tables.php');
//---
$tests_floders = [];
foreach (glob('tests/*.php') as $file) {
    $tests_floders[] = basename($file, '.php');
};
//---
$lis = '';
#---
foreach ($tests_floders as $item) {
    $lis .= "<li id='$item' class='nav-item'><a class='linknave' href='tests.php?te=$item'>$item</a></li>";
};
//---
echo <<<HTML
    <div class='row content'>
        <div class='col-md-2'>
            <ul class='flex-column'>
                $lis
            </ul>
        </div>
        <div class='px-0 col-md-10'>
            <div class='container-fluid'>
                <div class='card'>
HTML;
//---
$te = $_REQUEST['te'] ?? 'email';
//---
if (in_array($te, $tests_floders)) {
	require "tests/$te.php";
} else {
	require 'coordinator/404.php';
};
//---
if (isset($te)) {
	echo "<script>$('#" . $te . "').addClass('active');</script>";
};
//---
echo <<<HTML
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
HTML;
//---
require 'foter.php';
//---
?>