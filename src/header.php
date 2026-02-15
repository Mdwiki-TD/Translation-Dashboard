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
use function TD\Render\Html\banner_alert;
use function SQLorAPI\Funcs\get_coordinator;
//---
include_once __DIR__ . '/backend/userinfos_wrap.php';
//---
include_once __DIR__ . '/frontend/include.php';
include_once __DIR__ . '/backend/include_first/include.php';
//---
include_once __DIR__ . '/head.php';
//---
echo print_full_head(!isset($_GET["noboot"]));
//---
$coordinators = array_column(get_coordinator(), 'active', 'user');

$user_in_coord = false;
if (($coordinators[$GLOBALS['global_username']] ?? 0) == 1) {
	$user_in_coord = true;
};
$GLOBALS['user_in_coord'] = $user_in_coord;
define('user_in_coord', $user_in_coord);
//---
// var_dump(json_encode($coordinators2, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
//---
$coord_tools = "";
//---
// if (in_array($GLOBALS['global_username'], $coordinators)) {
if ($GLOBALS['user_in_coord'] === true) {
	$coord_tools = '<a href="/tdc/index.php" class="nav-link py-2 px-0 px-lg-2"><span class="navtitles"></span> <i class="bi bi-tools me-1"></i> Coordinator Tools</a>';
};
//---
$li_user = <<<HTML
	<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6">
		<a role="button" class="nav-link py-2 px-0 px-lg-2" onclick="login()">
			<i class="fas fa-sign-in-alt fa-sm fa-fw mr-2"></i> <span class="navtitles">Login</span>
		</a>
	</li>
HTML;
//---
if (!empty($GLOBALS['global_username'] ?? "")) {
	$u_name = $GLOBALS['global_username'];
	$li_user = <<<HTML
	<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6">
			<a href="leaderboard.php?get=users&user=$u_name" class="nav-link py-2 px-0 px-lg-2">
				<i class="fas fa-user fa-sm fa-fw mr-2"></i> <span class="navtitles">$u_name</span>
			</a>
		</li>
		<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6">
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
<svg xmlns="http://www.w3.org/2000/svg" style="display:none;">
	<symbol id="logo-xtools" viewBox="0 0 742 319">
		<g transform="translate(-84.236328,-656.44221)">
			<path
				d="m 254.95894,666.44732 c -1.22563,0.03 -2.44088,0.19665 -3.65039,0.43164 l 0.002,-0.004 c -1.61269,0.31328 -3.21586,0.74739 -4.81641,1.13867 -12.96358,3.16909 -23.6542,16.17806 -21.76757,29.9043 -0.27136,11.43572 0.12315,23.17366 -1.83008,34.48633 l -37.27149,70.74804 15.39649,25.99805 39.09765,-70.11328 c 6.07192,-7.5895 16.12788,-10.32734 24.51368,-14.6543 5.67827,-2.76279 11.91691,-4.59726 17.2539,-7.92383 8.97616,-8.78805 13.84599,-22.18169 12.36524,-34.66406 -0.2496,-2.99365 -3.31641,-1.38867 -3.31641,-1.38867 l -16.21289,12.0918 -16.13281,-8.42188 -5.01367,-12.29687 c 0,0 3.29754,-5.02106 5.08984,-5.77539 l 16.24023,-10.32032 c -2.24779,-7.14441 -12.23974,-8.85657 -18.71679,-9.21875 -0.41101,-0.023 -0.82192,-0.0276 -1.23047,-0.0176 z"
				fill="#069" />
			<path d="m 94.236328,965.38563 33.000002,0 53.50195,-102.38672 -14.72452,-25.01026 z" fill="#900" />
			<path d="m 168.67566,782.38636 -33.375,-63 -34.50001,0 105.75001,179.625 36,66.375 34.5,0 z" fill="#396" />
			<g fill="#484848">
				<path d="m 333.38392,817.30635 46.8,0 0,-19.92 -114.48,0 0,19.92 46.8,0 0,148.08 20.88,0 0,-148.08 z" />
				<path
					d="m 442.66517,965.38635 c 32.88,0 54.24,-24 54.24,-64.32 0,-40.56 -21.36,-64.08 -54.24,-64.08 -32.88,0 -54.24,24 -54.24,64.32 0,40.56 21.36,64.08 54.24,64.08 z m 0,-19.44 c -20.88,0 -33.12,-16.8 -33.12,-44.64 0,-28.56 12.24,-45.12 33.12,-45.12 20.88,0 33.12,17.04 33.12,44.88 0,28.08 -12.48,44.88 -33.12,44.88 z" />
				<path
					d="m 579.91767,965.38635 c 32.88,0 54.24,-24 54.24,-64.32 0,-40.56 -21.36,-64.08 -54.24,-64.08 -32.88,0 -54.24,24 -54.24,64.32 0,40.56 21.36,64.08 54.24,64.08 z m 0,-19.44 c -20.88,0 -33.12,-16.8 -33.12,-44.64 0,-28.56 12.24,-45.12 33.12,-45.12 20.88,0 33.12,17.04 33.12,44.88 0,28.08 -12.48,44.88 -33.12,44.88 z" />
				<path d="m 671.33017,962.50635 20.88,0 0,-173.28 -20.88,9.84 0,163.44 z" />
				<path
					d="m 772.37268,965.38635 c 26.64,0 43.68,-12.72 43.68,-34.8 0,-23.28 -17.52,-32.88 -39.84,-40.8 -13.68,-5.52 -25.92,-10.08 -25.92,-20.4 0,-8.64 7.92,-13.68 20.4,-13.68 13.2,0 21.84,6.24 27.12,11.76 l 14.4,-12 c -8.88,-10.56 -22.56,-18.48 -41.28,-18.48 -23.52,0 -41.28,12.24 -41.28,32.4 0,20.16 15.36,30 37.92,38.4 14.4,5.52 28.08,11.04 28.08,23.28 0,10.8 -9.84,15.6 -22.8,15.6 -13.2,0 -23.76,-7.92 -30,-14.64 l -14.88,12.72 c 9.6,12.24 26.4,20.64 44.4,20.64 z" />
			</g>
		</g>
	</symbol>
</svg>

<body>
	<header class="mb-3 border-bottom">
		<nav class="navbar navbar-expand-lg bg-body-tertiary shadow" id="mainnav">
			<div class="container-fluid" id="navbardiv">
				<a class="navbar-brand mb-0 h1" href="index.php" style="color:#0d6efd;">
					<img class='med-logo' width="40px" height="40px" src='/favicon.svg' decoding='async' alt='Wiki Project Med Foundation logo'>
					<span class='d-none d-sm-inline tool_title' title=''>WikiProjectMed Translation Dashboard</span>
					<span class='d-inline d-sm-none tool_title'>WikiProjectMed TD</span>
				</a>

				<div class="d-flex align-items-center order-lg-last">
					<button class="navbar-toggler me_ms_by_dir" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar"
						aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<button class="theme-toggle btn btn-link me-ms-auto" aria-label="Toggle theme">
						<i class="bi bi-moon-stars-fill"></i>
					</button>
				</div>
				<div class="collapse navbar-collapse" id="collapsibleNavbar">
					<ul class="navbar-nav flex-row flex-wrap bd-navbar-nav">
						<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6 <?php echo is_active('leaderboard.php'); ?>" id="leaderboard">
							<a class="nav-link py-2 px-0 px-lg-2" href="leaderboard.php">
								<span class="navtitles"> <i class="bi bi-bar-chart-line me-1"></i> Leaderboard</span>
							</a>
						</li>
						<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6" id="Prior">
							<a class="nav-link py-2 px-0 px-lg-2" target="_blank" href="/prior">
								<span class="navtitles">
									<i class="bi bi-bar-chart me-1"></i> Prior
								</span>
							</a>
						</li>
						<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6 <?php echo is_active('missing.php'); ?>" id="missing">
							<a class="nav-link py-2 px-0 px-lg-2" href="missing.php">
								<span class="navtitles">
									<i class="bi bi-card-list me-1"></i> Missing
								</span>
							</a>
						</li>
						<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6" id="coord">
							<?php echo $coord_tools; ?>
						</li>

						<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6">
							<a class="nav-link py-2 px-0 px-lg-2" href="https://github.com/MrIbrahem/Translation-Dashboard" target="_blank">
								<span class="navtitles">
									<i class="bi bi-github me-1"></i> Github
								</span>
							</a>
						</li>
						<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6">
							<span class="nav-link py-2 px-0 px-lg-2" id="load_time"></span>
						</li>
					</ul>
					<hr class="d-lg-none text-dark-subtle text-50">
					<ul class="navbar-nav flex-row flex-wrap bd-navbar-nav ms-lg-auto">
						<?php echo $li_user; ?>
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

	<?php

	$aal = banner_alert("Tool is down due to cultural and technical reasons since Aug 3 2024. Work is ongoing to get it functional again");
	// ---
	?>
	<main id="body">
		<!-- <div id="maindiv" class="container-fluid"> -->
		<div id="maindiv" class="container-fluid">

			<!-- <br> -->
			<!-- <hr/> -->
