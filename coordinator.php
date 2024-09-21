<?PHP
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
include_once __DIR__ . '/header.php';
//---
use function Actions\Functions\test_print;
use function Actions\HtmlSide\create_side;

echo <<<HTML
	<!-- </div> -->
	<script>$("#coord").addClass("active");</script>
	<!-- <div id="maindiv" class="container-fluid"> -->
HTML;
//---
include_once __DIR__ . '/actions/functions.php';
include_once __DIR__ . '/Tables/sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
//---
$filename = $_SERVER['SCRIPT_NAME'];
//---
function echo_card_start($filename)
{
	$sidebar = create_side($filename);
	echo <<<HTML
	<style>
		@media (min-width: 768px) {
			.colmd2 {
				width: 12% !important;
			}
			.colmd10 {
				width: 88% !important;
			}
		}

	</style>
		<div class='row content'>
			<!-- <div class='col-md-2 px-0' style="width: 10.66666667%;"> -->
			<div class='col-md-2 px-0 colmd2'>
				$sidebar
			</div>
			<div class='px-0 col-md-10 colmd10'>
				<div class='container-fluid'>
					<div class='card'>
	HTML;
}
//---
if (!isset($_REQUEST['nonav'])) {
	echo_card_start($filename);
};
//---
$ty = $_REQUEST['ty'] ?? 'last';
//---
if ($ty == 'translate_type') $ty = 'tt';
//---
// list of folders in coordinator
$corrd_folders = array_map('basename', glob('coordinator/admin/*', GLOB_ONLYDIR));
//---
$tools_folders = array_map(fn ($file) => basename($file, '.php'), glob('coordinator/tools/*.php'));
//---
test_print("corrd_folders" . json_encode($corrd_folders));
test_print("tools_folders" . json_encode($tools_folders));
//---
$adminfile = "coordinator/admin/$ty.php";
// if
if (in_array($ty, $tools_folders)) {
	require "coordinator/tools/$ty.php";
} elseif (in_array($ty, $corrd_folders) && user_in_coord) {
	require "coordinator/admin/$ty/index.php";
} elseif (is_file($adminfile) && user_in_coord) {
	require $adminfile;
} else {
	test_print("can't find $adminfile");
	require 'coordinator/404.php';
};
//---
function echo_card_end($ty)
{
	//---
	if (isset($ty)) {
		echo <<<HTML
			<script>
				$('#$ty').addClass('active');
				$("#$ty").closest('.mb-1').find('.collapse').addClass('show');
			</script>
		HTML;
	};
	//---
	echo <<<HTML
				</div>
			</div>
		</div>
	</div>
	HTML;
}
//---
echo_card_end($ty);
//---
echo "<script src='/Translation_Dashboard/js/autocomplate.js'></script>";
//---
include_once __DIR__ . '/foter.php';
//---
