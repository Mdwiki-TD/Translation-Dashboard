<!DOCTYPE html>
<HTML lang=en dir=ltr xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
	<meta name="robots" content="noindex">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Wiki Project Med Translation Dashboard</title>
<?php 
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
include_once('functions.php'); // $usrs
//---
include_once('login5.php');
//---
$hoste = '';
//---
function print_head() {
	global $username, $hoste;
	$hoste = 'https://tools-static.wmflabs.org/cdnjs';
	if ( $_SERVER['SERVER_NAME'] == 'localhost' )  $hoste = 'https://cdnjs.cloudflare.com';
	//---
	if (isset($_GET['noboot']) == '') {
		echo "
		<link href='dashboard_new1.css' rel='stylesheet' type='text/css'>
		<link href='$hoste/ajax/libs/font-awesome/5.15.3/css/all.min.css' rel='stylesheet' type='text/css'>
		<script src='$hoste/ajax/libs/jquery/3.6.3/jquery.min.js'></script>
		
		<!-- <script src='$hoste/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js'></script> -->
		<script src='$hoste/ajax/libs/popper.js/2.11.6/umd/popper.min.js'></script>
		<script src='$hoste/ajax/libs/twitter-bootstrap/5.2.3/js/bootstrap.min.js'></script>
		<link href='$hoste/ajax/libs/twitter-bootstrap/5.2.3/css/bootstrap.min.css' rel='stylesheet' type='text/css'>
		
		<script src='$hoste/ajax/libs/datatables.net/2.1.1/jquery.dataTables.min.js'></script>
		<script src='$hoste/ajax/libs/datatables.net-bs5/1.13.1/dataTables.bootstrap5.min.js'></script>
		<link href='$hoste/ajax/libs/datatables.net-bs5/1.13.1/dataTables.bootstrap5.css' rel='stylesheet' type='text/css'>

		<script src='sorttable.js'></script>
		<script src='to.js'></script>

		<style> 
		a {
			text-decoration: none;
		}</style>
		";
	};
	//---
	echo "<span id='myusername' style='display:none'>$username</span>
	";
	//---
};
//---
print_head();
//---
$coord = '';
//---
if (in_array($username, $usrs)) {
	$coord = '<a href="coordinator.php" class="nav-link"><span class="navtitles">Coordinator tools</span></a>';
};
//---
?>
</head>

<body>
<header>
	<nav id="mainnav" class="navbar navbar-expand-md bg-light shadow">
	   	<div class="container-fluid" id="navbardiv">
			<a class="navbar-brand mb-0 h1" href="index.php" style="color:blue;">Wiki Project Med Translation Dashboard</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="collapsibleNavbar">
				<ul class="navbar-nav me-auto">
					<li class="nav-item" id="leaderboard">
						<a href="leaderboard.php" class="nav-link">
							<span class="navtitles">Leaderboard</span>
						</a>
					</li>
					<li class="nav-item" style="display:none" id="myboard">
						<a href="leaderboard.php?user=<?php echo $username ?>" class="nav-link">
							<span class="navtitles">My Board</span>
						</a>
					</li>
					<li class="nav-item" id="missing">
						<a href="missing.php" class="nav-link">
							<span class="navtitles">Missing</span>
						</a>
					</li>
					<li class="nav-item" id="coord">
						<?php echo $coord; ?>
					</li>
					<li class="nav-item">
						<a href="https://github.com/MrIbrahem/Translation-Dashboard" target="_blank" class="nav-link">
							<span class="navtitles">Github</span>
						</a>
					</li>
				</ul>
				<div class="d-flex">
					<ul class="nav navbar-nav ml-auto">
						<li class="nav-item" id="">
							<span id="username_li" class="nav-link" style="display:none">
								<i class="fas fa-user fa-sm fa-fw mr-2"></i> <span class="navtitles" id="user_name"></span>
							</span>
						</li>
						<li class="nav-item">
							<a role="button" id="loginli" class="nav-link" onclick="login()">
								<i class="fas fa-sign-in-alt fa-sm fa-fw mr-2"></i><span class="navtitles">Login</span>
							</a>
						</li>
						<li class="nav-item">
							<a id="logout_btn" class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal" style="display:none">
								<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i><span class="navtitles">Logout</span>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</nav>
	<!-- Logout Modal-->
	<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title" id="exampleModalLabel">Ready to Leave?</h6>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">Select &quot;Logout&quot; below if you are ready to end your current session.</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
					<a class="btn btn-primary" href="login5.php?action=logout">Logout</a>
				</div>
			</div>
		</div>
	</div>
</header>
<script>	
	// $(document).ready(function() {
	var lo = $('#myusername').text();
	if ( lo != '' ) {
		$('#login_btn').hide();
		$("#doit_btn").show();
	
		$('#myboard').show();
		$('#loginli').hide();
	
		$('#username_li').show();
		$('#logout_btn').show();
		$('#user_name').text(lo);
	
	} else {
		$('#login_btn').show();
		$("#doit_btn").hide();
	
		$('#loginli').show();
	
		$('#username_li').hide();
		$('#logout_btn').hide();
	};
	// });
	</script>
<main id="body">
	<!-- <div id="maindiv" class="container-fluid"> -->
	<div id="maindiv" class="container-fluid">
	<br>