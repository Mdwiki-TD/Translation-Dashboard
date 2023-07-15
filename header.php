<!DOCTYPE html>
<HTML lang=en dir=ltr data-bs-theme="auto" xmlns="http://www.w3.org/1999/xhtml">
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
$testxx = isset($_REQUEST['test']) ? "1" : "";
//---
define('global_test', $testxx);
//---
include_once('functions.php'); // $usrs
//---
include_once('login5.php');
//---
define('global_username', $username);
//---
$hoste = '';
//---
function print_head() {
	global $hoste;
	$hoste = 'https://tools-static.wmflabs.org/cdnjs';
	if ( $_SERVER['SERVER_NAME'] == 'localhost' )  $hoste = 'https://cdnjs.cloudflare.com';
	//---
	if (isset($_GET['noboot']) == '') {
		echo <<<HTML
		<link rel="stylesheet" href="css/styles.css" type='text/css'>
		<link href='css/Responsive-Table.css' rel='stylesheet' type='text/css'>
		<link href='css/dashboard_new1.css' rel='stylesheet' type='text/css'>
		<link href='$hoste/ajax/libs/font-awesome/5.15.3/css/all.min.css' rel='stylesheet' type='text/css'>
		<script src='$hoste/ajax/libs/jquery/3.6.3/jquery.min.js'></script>
		
		<!-- <script src='$hoste/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js'></script> -->
		<script src='$hoste/ajax/libs/popper.js/2.11.6/umd/popper.min.js'></script>
		<script src='$hoste/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.min.js'></script>
		<link href='$hoste/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css' rel='stylesheet' type='text/css'>
		
		<script src='$hoste/ajax/libs/datatables.net/2.1.1/jquery.dataTables.min.js'></script>
		<script src='$hoste/ajax/libs/datatables.net-bs5/1.13.1/dataTables.bootstrap5.min.js'></script>
		<link href='$hoste/ajax/libs/datatables.net-bs5/1.13.1/dataTables.bootstrap5.css' rel='stylesheet' type='text/css'>

		<script type="module" src="js/color-modes.js"></script>
		<script src='js/sorttable.js'></script>
		<script src='js/to.js'></script>

		<style> 
		a {
			text-decoration: none;
		}</style>
		HTML;
	};
	//---
	echo "<span id='myusername' style='display:none'>" . global_username . "</span>";
	//---
};
//---
print_head();
//---
$user_in_coord = false;
$coord_tools = '<a href="tools.php" class="nav-link"><span class="navtitles"></span>Tools</a>';
//---
if (in_array(global_username, $usrs)) {
	$coord_tools = '<a href="coordinator.php" class="nav-link"><span class="navtitles"></span>Coordinator Tools</a>';
	$user_in_coord = true;
};
//---
define('user_in_coord', $user_in_coord);
//---
if (user_in_coord == true) {
	$testsline = <<<HTML
	<li class="nav-item" id="tests">
		<a href="tests.php" class="nav-link"><span class="navtitles"></span>Tests</a>
	</li>
	HTML;
};
//---
$ul = <<<HTML
				<ul class="navbar-nav me-auto">
					<li class="nav-item" id="leaderboard">
						<a href="leaderboard.php" class="nav-link">
							<span class="navtitles">Leaderboard</span>
						</a>
					</li>
					<li class="nav-item" style="display:none" id="myboard">
						<a href="leaderboard.php?user=$username" class="nav-link">
							<span class="navtitles">My Board</span>
						</a>
					</li>
					<li class="nav-item" id="missing">
						<a href="missing.php" class="nav-link">
							<span class="navtitles">Missing</span>
						</a>
					</li>
					<li class="nav-item" id="coord">$coord_tools</li>
					$testsline
					<li class="nav-item">
						<a href="https://github.com/MrIbrahem/Translation-Dashboard" target="_blank" class="nav-link">
							<span class="navtitles">Github</span>
						</a>
					</li>
				</ul>
HTML;
//---
$them_li = <<<HTML
			<li class="nav-item dropdown">
				<button class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static" aria-label="Toggle theme (light)">
				<svg class="bi my-1 theme-icon-active"><use href="#sun-fill"></use></svg>
				<span class="d-lg-none ms-2" id="bd-theme-text">Toggle theme</span>
				</button>
				<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme-text">
				<li>
					<button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="light" aria-pressed="true">
					<svg class="bi me-2 opacity-50 theme-icon"><use href="#sun-fill"></use></svg>
					Light
					<svg class="bi ms-auto d-none"><use href="#check2"></use></svg>
					</button>
				</li>
				<li>
					<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
					<svg class="bi me-2 opacity-50 theme-icon"><use href="#moon-stars-fill"></use></svg>
					Dark
					<svg class="bi ms-auto d-none"><use href="#check2"></use></svg>
					</button>
				</li>
				<li>
					<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
					<svg class="bi me-2 opacity-50 theme-icon"><use href="#circle-half"></use></svg>
					Auto
					<svg class="bi ms-auto d-none"><use href="#check2"></use></svg>
					</button>
				</li>
				</ul>
			</li>
HTML;
//---
?>
</head>

<body>
	
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
      <symbol id="bootstrap" viewBox="0 0 512 408" fill="currentcolor">
        <path d="M106.342 0c-29.214 0-50.827 25.58-49.86 53.32.927 26.647-.278 61.165-8.966 89.31C38.802 170.862 24.07 188.707 0 191v26c24.069 2.293 38.802 20.138 47.516 48.37 8.688 28.145 9.893 62.663 8.965 89.311C55.515 382.42 77.128 408 106.342 408h299.353c29.214 0 50.827-25.58 49.861-53.319-.928-26.648.277-61.166 8.964-89.311 8.715-28.232 23.411-46.077 47.48-48.37v-26c-24.069-2.293-38.765-20.138-47.48-48.37-8.687-28.145-9.892-62.663-8.964-89.31C456.522 25.58 434.909 0 405.695 0H106.342zm236.559 251.102c0 38.197-28.501 61.355-75.798 61.355h-87.202a2 2 0 01-2-2v-213a2 2 0 012-2h86.74c39.439 0 65.322 21.354 65.322 54.138 0 23.008-17.409 43.61-39.594 47.219v1.203c30.196 3.309 50.532 24.212 50.532 53.085zm-84.58-128.125h-45.91v64.814h38.669c29.888 0 46.373-12.03 46.373-33.535 0-20.151-14.174-31.279-39.132-31.279zm-45.91 90.53v71.431h47.605c31.12 0 47.605-12.482 47.605-35.941 0-23.46-16.947-35.49-49.608-35.49h-45.602z"/>
      </symbol>
      <symbol id="check2" viewBox="0 0 16 16">
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
        <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
      </symbol>
    </svg>

<header>
	<nav id="mainnav" class="navbar navbar-expand-lg shadow bg-light">
	   	<div class="container-fluid" id="navbardiv">
			<a class="navbar-brand mb-0 h1" href="index.php" style="color:blue;">
				<span class='d-none d-sm-inline'>WikiProjectMed Translation Dashboard</span>
				<span class='d-inline d-sm-none'>WikiProjectMed TD</span>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="collapsibleNavbar">
					<?php echo $ul; ?>
				<div class="d-flex">
					<ul class="navbar-nav flex-row flex-wrap ms-md-auto">
						<?php echo $them_li; ?>
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
								<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i><span class="navtitles"></span>
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