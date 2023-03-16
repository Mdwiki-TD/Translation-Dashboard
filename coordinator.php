<?PHP
//---
require('header.php');
//---
if ($user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
echo '</div>
<script>$("#coord").addClass("active");</script>
<div id="maindiv" class="container-fluid">';
//---
include_once('functions.php');
include_once('sql_tables.php'); // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
//---
$gg = '';
//---
$ty = $_REQUEST['ty'];
//---
if (!isset($_REQUEST['nonav'])) {
	//---
	$li = "<li id='%s' class='nav-item'><a class='linknave' href='coordinator.php?ty=%s'>%s</a></li>";
	//---
	$Translations_tab = array(
		array('id' => 'last',		'href' => 'last', 	'title' => 'Recent'),
		array('id' => 'process',	'href' => 'process',	'title' => 'In process'),
		array('id' => 'Pending',	'href' => 'Pending',	'title' => 'In process (total)'),
		array('id' => 'add',	'href' => 'add',	'title' => 'Add'),
	);
	//---
	$lis1 = '';
	foreach ($Translations_tab as $a => $item) {
		$lis1 .= sprintf($li, $item['id'], $item['href'], $item['title']);
	};
	//---
	$users_tab = array(
		array('id' => 'Emails', 	'href' => 'Emails', 		'title' => 'Emails'),
		array('id' => 'projects', 	'href' => 'projects', 		'title' => 'Projects'),
	);
	//---
	$lis2 = '';
	foreach ($users_tab as $a => $item) {
		$lis2 .= sprintf($li, $item['id'], $item['href'], $item['title']);
	};
	//---
	$Others_tab = array(
		array('id' => 'coordinators', 'href' => 'coordinators', 'title' => 'Coordinators'),
		array('id' => 'Campaigns', 	'href' => 'Campaigns', 	'title' => 'Campaigns'),
		array('id' => 'stat', 	'href' => 'stat', 	'title' => 'Status'),
		array('id' => 'settings', 	'href' => 'settings', 		'title' => 'Settings'),
	);
	//---
	$lis3 = '';
	foreach ($Others_tab as $a => $item) {
		$lis3 .= sprintf($li, $item['id'], $item['href'], $item['title']);
	};
	//---
	$Tools_tab = array(
		array('id' => 'wikirefs_options', 'href' => 'wikirefs_options', 'title' => 'Fixwikirefs (options)'),
	);
	//---
	$lis4 = '';
	foreach ($Tools_tab as $a => $item) {
		$lis4 .= sprintf($li, $item['id'], $item['href'], $item['title']);
	};
	//---
	$home1 = "
<span class='d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none border-bottom'>
	<a class='nav-link' href='coordinator.php'>
		<span id='Home' class='fs-5 fw-semibold'>Coordinator tools</span>
	</a>
</span>";
	//---
	$sidebar = "
	<div class='col-md-2'>
		$home
		<span class='fs-6 fw-semibold'>Translations:</span>
		<ul class='flex-column'>
			$lis1
		</ul>
		<span class='fs-6 fw-semibold'>Users:</span>
		<ul class='flex-column'>
			$lis2
		</ul>
		<span class='fs-6 fw-semibold'>Others:</span>
		<ul class='flex-column'>
			$lis3
		</ul>
		<span class='fs-6 fw-semibold'>Tools:</span>
		<ul class='flex-column'>
			<li id='fixwikirefs' class='nav-item'><a target='_blank' class='linknave' href='../fixwikirefs.php'>Fixwikirefs</a></li>
			$lis4
		</ul>
	</div>";
	//---
	echo "
    <div class='row content'>
		$sidebar
        <div class='col-md-10'>
            <div class='container-fluid'>
                <div class='card'>
	";
};
//---
if (isset($_GET['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
function get_request_1( $key, $i ) {
    $uu = isset($_REQUEST[$key][$i]) ? $_REQUEST[$key][$i] : '';
    return $uu;
};
//---
if (!isset($ty)) {
	require('coordinator/last.php');
	$ty = 'last';
} else {
	$file = "coordinator/$ty.php";
	if (file_get_contents($file)) {
		require($file);
	} else {
		require('coordinator/404.php');
	};
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
require('foter.php');
//---
?>