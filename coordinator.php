<?PHP
//---
require('header.php');
echo '</div>
<div id="maindiv" class="container-fluid">';
include_once('functions.php'); // $usrs
//---
$gg = '';
//---
$ty = $_REQUEST['ty'];
//---
if (!isset($_REQUEST['nonav'])) {
	//---
	$li = "<li id='%s' class='nav-item'><a class='linknave' href='%s'>%s</a></li>";
	//---
	$items1 = array(
		array('id' => 'last',		'href' => 'coordinator.php?ty=last', 	'title' => 'Recent'),
		array('id' => 'Pending',	'href' => 'coordinator.php?ty=Pending',	'title' => 'In process'),
		array('id' => 'add',	'href' => 'coordinator.php?ty=add',	'title' => 'Add'),
	);
	//---
	$lis1 = '';
	foreach ($items1 as $a => $item) {
		$lis1 .= sprintf($li, $item['id'], $item['href'], $item['title']);
	};
	//---
	$items2 = array(
		array('id' => 'Emails', 	'href' => 'coordinator.php?ty=Emails', 		'title' => 'Emails'),
		array('id' => 'projects', 	'href' => 'coordinator.php?ty=projects', 		'title' => 'Projects'),
	);
	//---
	$lis2 = '';
	foreach ($items2 as $a => $item) {
		$lis2 .= sprintf($li, $item['id'], $item['href'], $item['title']);
	};
	//---
	$items3 = array(
		array('id' => 'coordinators', 'href' => 'coordinator.php?ty=coordinators', 'title' => 'Coordinators'),
		array('id' => 'Campaigns', 	'href' => 'coordinator.php?ty=Campaigns', 	'title' => 'Campaigns'),
		// array('id' => 'others', 	'href' => 'coordinator.php?ty=others', 		'title' => 'Others'),
	);
	//---
	$lis3 = '';
	foreach ($items3 as $a => $item) {
		$lis3 .= sprintf($li, $item['id'], $item['href'], $item['title']);
	};
	//---
	echo '
    <div class="row content">
        <div class="col-md-2">
			<span class="d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none border-bottom">
				<a class="nav-link" href="coordinator.php">
					<span id="Home" class="fs-5 fw-semibold">Coordinator tools</span>
				</a>
			</span>
			<span class="fs-6 fw-semibold">Translations:</span>
			<ul class="flex-column">
				' . $lis1 . '
			</ul>
			<span class="fs-6 fw-semibold">Users:</span>
			<ul class="flex-column">
				' . $lis2 . '
			</ul>
			<span class="fs-6 fw-semibold">Others:</span>
			<ul class="flex-column">
				' . $lis3 . '
			</ul>
			<span class="fs-6 fw-semibold">Tools:</span>
			<ul class="flex-column">
				<li id="fixwikirefs" class="nav-item"><a target="_blank" class="linknave" href="../fixwikirefs.php">Fixwikirefs</a></li>
			</ul>
        </div>
        <div class="col-md-10">
            <div class="container-fluid">
                <div class="card">
                    
	';
};
//---
if ($_GET['test'] != '') {

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
if (!in_array($username, $usrs)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
if (!isset($ty)) {
	require('coordinator/index.php');
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