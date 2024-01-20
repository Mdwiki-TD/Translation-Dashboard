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
/*
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};*/
//---
echo '</div>
<script>$("#coord").addClass("active");</script>
<div id="maindiv" class="container-fluid">';
//---
include_once 'functions.php';
include_once 'sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
//---
$gg = '';
//---
$filename = $_SERVER['SCRIPT_NAME'];
//---
if (!isset($_REQUEST['nonav'])) {
	$sidebar = create_side($filename);
	echo <<<HTML
		<div class='row content'>
			<div class='col-md-2'>
				$sidebar
			</div>
			<div class='px-0 col-md-10'>
				<div class='container-fluid'>
					<div class='card'>
	HTML;
};
//---
$ty = $_REQUEST['ty'] ?? 'last';
//---
$corrd_floders = [];
foreach (glob('coordinator/admin/*.php') as $file) $corrd_floders[] = basename($file, '.php');
//---
$tools_floders = [];
foreach (glob('coordinator/tools/*.php') as $file) $tools_floders[] = basename($file, '.php');
//---
test_print($corrd_floders);
test_print($tools_floders);
//---
$adminfile = "coordinator/admin/$ty.php";
// if 
if (in_array($ty, $tools_floders)) {
	require "coordinator/tools/$ty.php";
} elseif (in_array($ty, $corrd_floders) && user_in_coord) {
	require $adminfile;
} elseif (is_file($adminfile) && user_in_coord) {
	require $adminfile;
} else {
	require 'coordinator/404.php';
};
//---
if (isset($ty)) {
	$gg = "<script>$('#" . $ty . "').addClass('active');</script>";
};
//---
echo $gg;
//---
print "
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>";
//---
require 'foter.php';
//---
?>