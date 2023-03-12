<?PHP
//---
require('header.php');
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
	$li = "<li id='%s' class='nav-item'><a class='linknave' href='tools.php?ty=%s'>%s</a></li>";
	//---
	$Translations_tab = array(
		array('id' => 'last',		'href' => 'last', 	'title' => 'Recent'),
		array('id' => 'process',	'href' => 'process',	'title' => 'In process'),
		array('id' => 'Pending',	'href' => 'Pending',	'title' => 'In process (total)'),
	);
	//---
	$lis1 = '';
	foreach ($Translations_tab as $a => $item) {
		$lis1 .= sprintf($li, $item['id'], $item['href'], $item['title']);
	};
	//---
	$home1 = "
<span class='d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none border-bottom'>
	<a class='nav-link' href='tools.php'>
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
if (!in_array($username, $usrs)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
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