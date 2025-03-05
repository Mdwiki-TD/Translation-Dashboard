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
// set env
$tables_dir = __DIR__ . '/../td/Tables';

if (substr($tables_dir, 0, 2) == 'I:') {
	// $tables_dir = __DIR__ . '/../mdwiki/public_html/td/Tables';
	$tables_dir = 'I:/mdwiki/mdwiki/public_html/td/Tables';
}
//---
if (!getenv('tables_dir')) {
	putenv('tables_dir=' . $tables_dir);
}
//---
include_once __DIR__ . '/actions/functions.php'; // $coordinators
//---
include_once __DIR__ . '/../auth/auth/user_infos.php';
//---
$user_in_coord = false;
//---
if (in_array(global_username, $coordinators)) {
	$user_in_coord = true;
};
//---
define('user_in_coord', $user_in_coord);
//---
include_once __DIR__ . '/head.php';
//---
use function Actions\Html\banner_alert;
//---
echo "
</head>";
//---
$hoste = '';
//---
$coord_tools = "";
//---
if (in_array(global_username, $coordinators)) {
	$coord_tools = '<a href="/tdc/index.php" class="nav-link py-2 px-0 px-lg-2"><span class="navtitles"></span>Coordinator Tools</a>';
};
//---
$them_li = <<<HTML
	<button class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown"
		data-bs-display="static" aria-label="Toggle theme (light)">
		<span class="theme-icon-active my-1">
			<i class="bi bi-sun-fill"></i>
		</span>
		<span class="d-lg-none ms-2" id="bd-theme-text"></span>
	</button>
	<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme-text">
		<li>
			<button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="light" aria-pressed="true">
				<i class="bi bi-sun-fill me-2 opacity-50 theme-icon"></i> Light
			</button>
		</li>
		<li>
			<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
				<i class="bi bi-moon-stars-fill me-2 opacity-50 theme-icon"></i> Dark
			</button>
		</li>
		<li>
			<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
				<i class="bi bi-circle-half me-2 opacity-50 theme-icon"></i> Auto
			</button>
		</li>
	</ul>
HTML;
//---

$li_user = <<<HTML
	<li class="nav-item col-4 col-lg-auto">
		<a role="button" class="nav-link py-2 px-0 px-lg-2" onclick="login()">
			<i class="fas fa-sign-in-alt fa-sm fa-fw mr-2"></i> <span class="navtitles">Login</span>
		</a>
		</li>
HTML;
//---
if (defined('global_username') && global_username != '') {
	$u_name = global_username;
	$li_user = <<<HTML
	<li class="nav-item col-4 col-lg-auto">
			<a href="leaderboard.php?user=$u_name" class="nav-link py-2 px-0 px-lg-2">
				<i class="fas fa-user fa-sm fa-fw mr-2"></i> <span class="navtitles">$u_name</span>
			</a>
		</li>
		<li class="nav-item col-4 col-lg-auto">
			<a class="nav-link py-2 px-0 px-lg-2" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
				<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> <span class="d-lg-none navtitles">Logout</span>
			</a>
		</li>
	HTML;
};
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
						$li_user
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
						<a class="btn btn-outline-primary" href="/auth/index.php?a=logout">Logout</a>
					</div>
				</div>
			</div>
		</div>
	</header>
HTML;

$aal = banner_alert("Tool is down due to cultural and technical reasons since Aug 3 2024. Work is ongoing to get it functional again");
// ---
?>
<main id="body">
	<!-- <div id="maindiv" class="container-fluid"> -->
	<div id="maindiv" class="container-fluid">

		<!-- <br> -->
		<!-- <hr/> -->
