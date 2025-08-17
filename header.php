<?php
//---
$time_start = microtime(true);
//---
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
//---
ini_set('session.use_strict_mode', '1');
//---
// include_once __DIR__ . '/actions/load_request.php';
include_once __DIR__ . '/../auth/auth/user_infos.php';
//---
if (isset($GLOBALS['global_username']) && $GLOBALS['global_username'] != '') {
	$global_username = $GLOBALS['global_username'];
} else {
	$GLOBALS['global_username'] = '';
}
//---
include_once __DIR__ . '/head.php';
include_once __DIR__ . '/Tables/tables_dir.php';
include_once __DIR__ . '/actions/test_print.php';
include_once __DIR__ . '/actions/mdwiki_sql.php';
include_once __DIR__ . '/actions/html.php';
include_once __DIR__ . '/actions/td_api.php';
include_once __DIR__ . '/api_or_sql/index.php';
//---
use function Actions\Html\banner_alert;
use function SQLorAPI\Funcs\get_coordinator;
//---
$coordinators = array_column(get_coordinator(), 'user');

$user_in_coord = false;
if (in_array($GLOBALS['global_username'], $coordinators)) {
	$user_in_coord = true;
};
$GLOBALS['user_in_coord'] = $user_in_coord;
define('user_in_coord', $user_in_coord);
//---
// var_dump(json_encode($coordinators2, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
//---
$coord_tools = "";
//---
if (in_array($GLOBALS['global_username'], $coordinators)) {
	$coord_tools = '<a href="/tdc/index.php" class="nav-link py-2 px-0 px-lg-2"><span class="navtitles"></span>Coordinator Tools</a>';
};
//---
$li_user = <<<HTML
	<li class="nav-item col-6 col-lg-auto">
		<a role="button" class="nav-link py-2 px-0 px-lg-2" onclick="login()">
			<i class="fas fa-sign-in-alt fa-sm fa-fw mr-2"></i> <span class="navtitles">Login</span>
		</a>
	</li>
HTML;
//---
if (isset($GLOBALS['global_username']) && $GLOBALS['global_username'] != '') {
	$u_name = $GLOBALS['global_username'];
	$li_user = <<<HTML
	<li class="nav-item col-6 col-lg-auto">
			<a href="leaderboard.php?get=users&user=$u_name" class="nav-link py-2 px-0 px-lg-2">
				<i class="fas fa-user fa-sm fa-fw mr-2"></i> <span class="navtitles">$u_name</span>
			</a>
		</li>
		<li class="nav-item col-6 col-lg-auto">
			<a class="nav-link py-2 px-0 px-lg-2" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
				<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> <span class="d-lg-none navtitles">Logout</span>
			</a>
		</li>
	HTML;
};
//---
// get php file name from url http://localhost:9001/Translation_Dashboard/leaderboard.php?get=langs&langcode=zh
function is_active($url)
{
	$file_name = basename($_SERVER['PHP_SELF']);
	// echo "file_name: $file_name <br>";
	//---
	if ($file_name == $url) {
		return 'active';
	}
	//---
	return '';
}
//---
?>

<body>
	<header class="mb-3 border-bottom">
		<nav class="navbar navbar-expand-lg bg-body-tertiary shadow" id="mainnav">
			<div class="container-fluid" id="navbardiv">
				<a class="navbar-brand mb-0 h1" href="index.php" style="color:#0d6efd;">
					<span class='d-none d-sm-inline tool_title' title=''>WikiProjectMed Translation Dashboard</span>
					<span class='d-inline d-sm-none tool_title'>WikiProjectMed TD</span>
				</a>
				<button class="navbar-toggler me_ms_by_dir" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar"
					aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="collapsibleNavbar">
					<ul class="navbar-nav flex-row flex-wrap bd-navbar-nav">
						<li class="nav-item col-6 col-lg-auto <?php echo is_active('leaderboard.php'); ?>" id="leaderboard">
							<a class="nav-link py-2 px-0 px-lg-2" href="leaderboard.php">
								<span class="navtitles">Leaderboard</span>
							</a>
						</li>
						<li class="nav-item col-6 col-lg-auto" id="Prior">
							<a class="nav-link py-2 px-0 px-lg-2" target="_blank" href="/prior">
								<span class="navtitles">Prior</span>
							</a>
						</li>
						<li class="nav-item col-6 col-lg-auto <?php echo is_active('missing.php'); ?>" id="missing">
							<a class="nav-link py-2 px-0 px-lg-2" href="missing.php">
								<span class="navtitles">Missing</span>
							</a>
						</li>
						<li class="nav-item col-6 col-lg-auto" id="coord">
							<?php echo $coord_tools; ?>
						</li>

						<li class="nav-item col-6 col-lg-auto">
							<a class="nav-link py-2 px-0 px-lg-2" href="https://github.com/MrIbrahem/Translation-Dashboard" target="_blank">
								<span class="navtitles">Github</span>
							</a>
						</li>
						<li class="nav-item col-6 col-lg-auto">
							<span class="nav-link py-2 px-0 px-lg-2" id="load_time"></span>
						</li>
					</ul>
					<hr class="d-lg-none text-dark-subtle text-50">
					<ul class="navbar-nav flex-row flex-wrap bd-navbar-nav ms-lg-auto">
						<?php echo $li_user; ?>
					</ul>
				</div>
				<div class="d-flex">
					<button class="theme-toggle btn btn-link ms-me-auto" aria-label="Toggle theme">
						<i class="bi bi-moon-stars-fill"></i>
					</button>
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

	<?php

	$aal = banner_alert("Tool is down due to cultural and technical reasons since Aug 3 2024. Work is ongoing to get it functional again");
	// ---
	?>
	<main id="body">
		<!-- <div id="maindiv" class="container-fluid"> -->
		<div id="maindiv" class="container-fluid">

			<!-- <br> -->
			<!-- <hr/> -->
