<?php
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
ini_set('session.use_strict_mode', '1');
//---
define('root_dir', __DIR__);
//---
$testxx = isset($_GET['test']) ? $_GET['test'] : "";
//---
if (!defined('global_test')) {
	define('global_test', $testxx);
};
//---
$testhtml = htmlspecialchars($testxx, ENT_QUOTES, 'UTF-8');
//---
echo <<<HTML
	<input type='hidden' id='test' value='$testhtml'>
HTML;
//---
include_once 'actions/functions.php'; // $usrs
include_once 'auth/index.php';
//---
define('global_username', $username);
//---
$hoste = '';
//---
include_once 'head.php';
//---
echo "
<span id='myusername' style='display:none'>" . global_username . "</span>";
//---
echo "
</head>";
//---
$user_in_coord = false;
$coord_tools = '<a href="tools.php" class="nav-link py-2 px-0 px-lg-2"><span class="navtitles"></span>Tools</a>';
//---
if (in_array(global_username, $usrs)) {
	$coord_tools = '<a href="coordinator.php" class="nav-link py-2 px-0 px-lg-2"><span class="navtitles"></span>Coordinator Tools</a>';
	$user_in_coord = true;
};
//---
define('user_in_coord', $user_in_coord);
//---
$testsline = '';
//---
if (user_in_coord == true) {
	$testsline = <<<HTML
	<li class="nav-item col-4 col-lg-auto" id="tests">
		<a class="nav-link py-2 px-0 px-lg-2" href="tests.php"><span class="navtitles"></span>Tests</a>
	</li>
	HTML;
};
//---
require("darkmode.php");
$them_li = dark_mode_icon();
//---
echo <<<HTML
<body>
<header class="mb-3 border-bottom">
	<nav id="mainnav" class="navbar navbar-expand-lg shadow">
	   	<div class="container-fluid" id="navbardiv">
			<a class="navbar-brand mb-0 h1" href="index.php" style="color:#0d6efd;">
				<span class='d-none d-sm-inline'>WikiProjectMed Translation Dashboard</span>
				<span class='d-inline d-sm-none'>WikiProjectMed TD</span>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar"
				aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="collapsibleNavbar">
				<ul class="navbar-nav flex-row flex-wrap bd-navbar-nav">
					<li class="nav-item col-4 col-lg-auto" id="leaderboard">
						<a class="nav-link py-2 px-0 px-lg-2" href="leaderboard.php">
							<span class="navtitles">Leaderboard</span>
						</a>
					</li>
					<li class="nav-item col-4 col-lg-auto" id="Prior">
						<a class="nav-link py-2 px-0 px-lg-2" target="_blank"  href="/prior/index.php">
							<span class="navtitles">Prior</span>
						</a>
					</li>
					<li class="nav-item col-4 col-lg-auto" id="missing">
						<a class="nav-link py-2 px-0 px-lg-2" href="missing.php">
							<span class="navtitles">Missing</span>
						</a>
					</li>
					<li class="nav-item col-4 col-lg-auto" id="coord">$coord_tools</li>
					$testsline
					<li class="nav-item col-4 col-lg-auto">
						<a class="nav-link py-2 px-0 px-lg-2" href="https://github.com/MrIbrahem/Translation-Dashboard" target="_blank">
							<span class="navtitles">Github</span>
						</a>
					</li>
				</ul>
				<hr class="d-lg-none text-black-50">
				<ul class="navbar-nav flex-row flex-wrap bd-navbar-nav ms-lg-auto">
					<li class="nav-item col-4 col-lg-auto dropdown">
						$them_li
					</li>
					<li class="nav-item col-4 col-lg-auto" id="">
						<a id="username_li" href="leaderboard.php?user=$username" class="nav-link py-2 px-0 px-lg-2" style="display:none">
							<i class="fas fa-user fa-sm fa-fw mr-2"></i> <span class="navtitles" id="user_name"></span>
						</a>
					</li>
					<li class="nav-item col-4 col-lg-auto" id="loginli">
						<a role="button" class="nav-link py-2 px-0 px-lg-2" onclick="login()">
							<i class="fas fa-sign-in-alt fa-sm fa-fw mr-2"></i> <span class="navtitles">Login</span>
						</a>
					</li>
					<li class="nav-item col-4 col-lg-auto">
						<a id="logout_btn" class="nav-link py-2 px-0 px-lg-2" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal" style="display:none">
							<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> <span class="d-lg-none navtitles">Logout</span>
						</a>
					</li>
				</ul>
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
					<a class="btn btn-outline-primary" href="auth.php?a=logout">Logout</a>
				</div>
			</div>
		</div>
	</div>
</header>
HTML;
?>
<script>
	// $(document).ready(function() {
	var lo = $('#myusername').text();
	if (lo != '') {
		$('#myboard').show();
		$('#user_name').text(lo);

		$('#login_btn, #loginli').hide();
		$("#doit_btn, #username_li, #logout_btn").show();

	} else {
		$('#login_btn, #loginli').show();
		$("#doit_btn, #username_li, #logout_btn").hide();
	};
	// });
</script>
<main id="body">
	<!-- <div id="maindiv" class="container-fluid"> -->
	<div id="maindiv" class="container-fluid">
		<!-- <br> -->
		<!-- <hr/> -->
