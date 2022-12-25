<?PHP
//---
require('header.php');
require('functions.php');
//---
$gg = '';
//---
$ty = $_REQUEST['ty'];
//---
echo '</div>
<div class="container-fluid">
  <div class="row content">
    <div class="col-sm-2 sidenav">
      <h4>coordinator tools</h4>
      <ul class="nav nav-pills nav-stacked">
        <li id="Home"><a href="coordinator.php">Home</a></li>
        <li id="Campaigns"><a href="coordinator.php?ty=Campaigns">Campaigns</a></li>
        <li id="Emails"><a href="coordinator.php?ty=Emails">Emails</a></li>
        <li id="others"><a href="coordinator.php?ty=others">Others</a></li>
      </ul><br>
    </div>
    <div class="col-sm-10">
      <div class="panel panel-default">
        <div class="panel-body">
';
//---
if ($_GET['test'] != '') {
	// echo(__file__);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
if (!isset($ty)) {
	require('coordinator/index.php');
	$ty = 'Home';
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
<div>";
//---
require('foter.php');
//---
?>