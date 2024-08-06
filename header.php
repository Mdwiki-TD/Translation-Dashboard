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
define('root_dire', __DIR__);
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
include_once __DIR__ . '/actions/functions.php'; // $usrs
//---
include_once __DIR__ . '/auth/user_infos.php';
//---
if (defined('global_username')) {
	echo "<span id='myusername' style='display:none'>" . global_username . "</span>";
};
//---
include_once __DIR__ . '/head.php';
//---
echo "
</head>";
//---
$hoste = '';
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

$aal = <<<HTML
	<div class='container'>
		<div class="alert alert-danger d-flex align-items-center" role="alert">
			<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
				<symbol id="check-circle-fill" viewBox="0 0 16 16">
					<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
				</symbol>
				<symbol id="info-fill" viewBox="0 0 16 16">
					<path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
				</symbol>
				<symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
					<path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
				</symbol>
			</svg>
			<svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:">
				<use xlink:href="#exclamation-triangle-fill" />
			</svg>
			<div>
				Tool is down due to cultural and technical reasons since Aug 3 2024. Work is ongoing to get it functional again
			</div>
		</div>
	</div>
	HTML;
?>
<script>
	var lo = $('#myusername').text();
	// get username from cookie
	// var lo = getCookie('username');
	if (lo != '') {
		$('#myboard').show();
		$('#user_name').text(lo);

		$('#login_btn, #loginli').hide();
		$("#doit_btn, #username_li, #logout_btn").show();

	} else {
		$('#login_btn, #loginli').show();
		$("#doit_btn, #username_li, #logout_btn").hide();
	};
</script>
<main id="body">
	<!-- <div id="maindiv" class="container-fluid"> -->
	<div id="maindiv" class="container-fluid">

		<!-- <br> -->
		<!-- <hr/> -->
