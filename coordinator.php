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
function create_side() {
	//---
	global $filename;
	//---
	$li = "<li class='nav-item col-3 col-lg-auto' id='%s'><a class='linknave' href='$filename?ty=%s'>%s</a></li>";
	$li_blank = "<li class='nav-item col-3 col-lg-auto' id='%s'><a target='_blank' class='linknave' href='%s'>%s</a></li>";
	//---
	$home1 = <<<HTML
		<span class='d-flex align-items-center pb-1 mb-1 text-decoration-none border-bottom'>
			<a class='nav-link' href='$filename'>
				<span id='Home' class='fs-5 fw-semibold'>Coordinator Tools</span>
			</a>
		</span>
	HTML;
	//---
	$sidebar = <<<HTML
	<div class='col-md-2'>
	<nav class="navbar-nav">
		$home1
	HTML;
	//---
	$main = array();
	//---
	$main['Translations'] = array(
		array('id' => 'last',		'admin' => 0,	'href' => 'last', 		'title' => 'Recent'),
		array('id' => 'process',	'admin' => 0,	'href' => 'process',	'title' => 'In process'),
		array('id' => 'Pending',	'admin' => 0,	'href' => 'Pending',	'title' => 'In process (total)'),
		array('id' => 'add',		'admin' => 1,	'href' => 'add',		'title' => 'Add'),
		array('id' => 'translate_type',		'admin' => 1,	'href' => 'translate_type',		'title' => 'Translate Type'),
	);
	//---
	$main['Users'] = array(
		array('id' => 'Emails', 	'admin' => 1,	'href' => 'Emails', 		'title' => 'Emails'),
		array('id' => 'projects', 	'admin' => 1,	'href' => 'projects', 		'title' => 'Projects'),
	);
	//---
	$main['Others'] = array(
		array('id' => 'coordinators', 	'admin' => 1,	'href' => 'coordinators', 	'title' => 'Coordinators'),
		array('id' => 'Campaigns', 		'admin' => 1,	'href' => 'Campaigns', 		'title' => 'Campaigns'),
		array('id' => 'stat', 			'admin' => 0,	'href' => 'stat', 			'title' => 'Status'),
		array('id' => 'settings', 		'admin' => 1,	'href' => 'settings', 		'title' => 'Settings'),
	);
	//---
	$main['Tools'] = array(
		array('id' => 'wikirefs_options', 	'admin' => 1,	'href' => 'wikirefs_options', 		'title' => 'Fixwikirefs (options)'),
		array('id' => 'fixwikirefs', 		'admin' => 0,	'href' => '../fixwikirefs.php', 	'title' => 'Fixwikirefs', 'target' => '_blank'),
	);
	//---
	foreach ($main as $key => $items) {
		$lis = '';
		foreach ($items as $a => $item) {
			$target = $item['target'] ?? '';
			//---
			$admin  = $item['admin'] ?? 0;
			//---
			if ($admin == 1 && user_in_coord == false) continue;
			//---
			if ($target != '') {
				$lis .= sprintf($li_blank, $item['id'], $item['href'], $item['title']);
			} else {
				$lis .= sprintf($li, $item['id'], $item['href'], $item['title']);
			};
		};
		if ($lis != '') {
			$sidebar .= <<<HTML
			<span class='fs-6 fw-semibold'>$key:</span>
			
			<ul class='navbar-nav flex-row flex-wrap d-lg-table d-md-table'>
				$lis
			</ul>
			
			HTML;
		}
		//---
	}
	//---
	$sidebar .= "
	</nav>
	</div>
	";
	//---
	return $sidebar;
};
//---
if (!isset($_REQUEST['nonav'])) {
	$sidebar = create_side();
	echo <<<HTML
		<div class='row content'>
			$sidebar
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
$file = "coordinator/$ty.php";
// if 
if (in_array($ty, $tools_floders)) {
	require "coordinator/tools/$ty.php";
} elseif (in_array($ty, $corrd_floders) && user_in_coord) {
	require "coordinator/admin/$ty.php";
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