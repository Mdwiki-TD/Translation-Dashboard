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

    <link href="dashboard_new.css" rel="stylesheet">
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
        <script src='$hoste/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
        <script src='$hoste/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js'></script>

        <link href='$hoste/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css' rel='stylesheet' type='text/css'>
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
	$co = '<a href="coordinator.php"><span style="font-size:16px">Coordinator tools</span></a>';
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
</head>

<body>
    <header>
        <nav class="navbar navbar-default navbar-expand shadow">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href='index.php' style="color:blue;">Wiki Project Med Translation Dashboard</a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" 
                    data-target="#main-navigation" aria-expanded="false" wfd-invisible="true">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="main-navigation">
                    <ul class="nav navbar-nav">
                        <!-- <li class="active"> -->
                        <li>
                            <a href="leaderboard.php"><span style="font-size:16px">Leaderboard</span></a>
                        </li>
                        <li>
                            <a id="myboard" style="display:none" href="my.php"><span style="font-size:16px">My Board</span></a>
                        </li>
                        <li>
                            <a href="missing.php"><span style="font-size:16px">Missing</span></a>
                        </li>
                        <li id='coord'><?php echo $coord; ?> </li>
                        <li>
                            <a href="https://github.com/MrIbrahem/Translation-Dashboard" target="_blank"><span style="font-size:16px">Github</span></a>
                        </li>
                    </ul>
                    <ul class='nav navbar-nav navbar-right'>
                        <li>
                            <div id="loginli" class="navbar-text">
                                <a class='nav-link' style="padding:2px;border:0px;" onclick="login()"><i class="fas fa-sign-in-alt fa-sm fa-fw mr-2 text-gray-600"></i> Login </a>
                            </div>
                        </li>
                        <li>
                            <div id='username_li' class="navbar-text" style="display:none">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400 d-none d-lg-inline"></i> <span id="user_name"></span>
                            </div>
                        </li>
                        <li>
                            <div id='logout-btn' class="navbar-text" style="display:none">
                            <a id="logout-btn" class="nav-link dropdown-toggle" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                <span class="text-gray-600">Logout</span>
                            </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
            <!-- Logout Modal-->
            <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
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
        $('#logout-btn').show();
        $('#user_name').text(lo);
    
    } else {
        $('#login_btn').show();
        $("#doit_btn").hide();
    
        $('#loginli').show();
    
        $('#username_li').hide();
        $('#logout-btn').hide();
    };
    // });
    </script>
<?PHP
// require('sidebar.php');  
?>
<main id="body">
    <div class='container'>