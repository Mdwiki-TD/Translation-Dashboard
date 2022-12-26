<!DOCTYPE html>
<HTML lang=en dir=ltr xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
	<meta name="robots" content="noindex">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Wiki Project Med Translation Dashboard</title>
	<!-- <script src="to.js"></script> -->
	<!-- <script src="sorttable.js"></script> -->
	<!-- Custom fonts for this template-->
	<!-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet"> -->
<?php 
	//---
	if ($_GET['test'] != '') {
		// echo(__file__);
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	};
	//---
	// include_once('sql.php');
	include_once('login5.php');
	//---
	$hoste = 'https://tools-static.wmflabs.org/cdnjs';
	if ( $_SERVER['SERVER_NAME'] == 'localhost' )  $hoste = 'https://cdnjs.cloudflare.com';
	#---
	if ($_GET['noboot'] == '') {
		echo "
		<link href='$hoste/ajax/libs/font-awesome/5.15.3/css/all.min.css' rel='stylesheet' type='text/css'>
		<script src='$hoste/ajax/libs/jquery/3.6.1/jquery.min.js'></script>
		
		<script src='$hoste/ajax/libs/popper.js/1.16.1/umd/popper.min.js'></script>
		<script src='$hoste/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.min.js'></script>
		<link href='$hoste/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css' rel='stylesheet' type='text/css'>
		
		<script src='$hoste/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js'></script>
		<link href='$hoste/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>
		<link href='$hoste/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css' rel='stylesheet' type='text/css'>
		";
	};
	//---
	echo '<span id="myusername" style="display:none">' . $username . '</span>
	';
	//---
	if ($_REQUEST['test'] != '' ) echo "<br>load " . str_replace ( __dir__ , '' , __file__ ) . " true.";
	//---
function get_coord_users($username) {
	//---
	/*$qua = 'select user from coordinator;';
	//---
	if ( isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost' ) { 
		$usrs = sqlquary_localhost($qua);
	} else {
		$usrs = quary($qua);
	};*/
	$co = '<a href="coordinator.php" class="nav-link"><span class="navtitles">Coordinator tools</span></a>';
	//---
	$usrs = Array("Mr. Ibrahem", "Doc James");
	//---
	foreach ( $usrs AS $k => $user ) {
		// echo $user['user'];
		if ($user == $username) {
			return $co;
		};
	};
	//---
	return '';
};
//---
	$coord = get_coord_users($username);
	//---
?>
	
	<!-- <link href="css/sb-admin-2.min.css" rel="stylesheet"> -->
	<link href="dashboard_new.css" rel="stylesheet">
</head>

<body>
<header>

<nav class="navbar navbar-expand-md bg-light navbar-light shadow">
    <a class="navbar-brand" href="index.php" style="color:blue;">Wiki Project Med Translation Dashboard</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
        </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
        <li class="nav-item">
          <a href="leaderboard.php" class="nav-link">
            <span class="navtitles">Leaderboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a id="myboard" style="display:none" href="my.php" class="nav-link">
            <span class="navtitles">My Board</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="missing.php" class="nav-link">
            <span class="navtitles">Missing</span>
          </a>
        </li>
        <li id="coord" class="nav-item">
          <?php echo $coord; ?>
        </li>
        <li class="nav-item">
          <a href="https://github.com/MrIbrahem/Translation-Dashboard" target="_blank" class="nav-link">
            <span class="navtitles">Github</span>
          </a>
        </li>
      </ul>
      <ul class="nav navbar-nav ml-auto">
        <li class="nav-item">
          <div id="loginli" class="nav-link">
            <a class="nav-link" style="padding:2px;border:0px;" onclick="login()">
              <i class="fas fa-sign-in-alt fa-sm fa-fw mr-2"></i>Login</a>
          </div>
        </li>
        <li class="nav-item">
            <a id="username_li" class="nav-link" style="display:none">
				<i class="fas fa-user fa-sm fa-fw mr-2"></i> <span class="navtitles" id="user_name"></span>
            </a>
        </li>
        <li class="nav-item">
            <a id="logout_btn" class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal" style="display:none">
              <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i><span class="navtitles">Logout</span>
            </a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="exampleModalLabel">Ready to Leave?</h6>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&#xD7;</span>
          </button>
        </div>
        <div class="modal-body">Select &quot;Logout&quot; below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="login5.php?action=logout">Logout</a>
        </div>
      </div>
    </div>
  </div>
</header>
<script>
	
	function login(){
		var cat = $('#cat').val();
		var depth = $('#depth').val();
		var code = $('#code').val();
		var type = $('input[name=type]:checked').val();
	
		var url = 'login5.php?action=login&code=' + code + '&cat=' + cat + '&depth=' + depth + '&type=' + type;
		window.location.href = url;
	}
	
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
<?PHP
// require('sidebar.php');	
?>
<main id="body">
	<div class="container-fluid">
	<br>