<!DOCTYPE html>
<HTML lang=en dir=ltr xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
<meta name="robots" content="noindex">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Wiki Project Med Translation Dashboard</title>

<script src="/jquery.min.js" type="text/javascript"></script>
<script src="sorttable.js"></script>
<script src="/bootstrap1.min.js" type="text/javascript"></script>

<link href="dashboard.css" rel="stylesheet">

<!--<link href="/mem.css" rel="stylesheet">-->
<link href="/bootstrap.min.css" rel="stylesheet">

</head>

<body wfd-invisible="true">

<header class="app-header">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navigation" aria-expanded="false" wfd-invisible="true">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brande active" href='index.php'>
                    Wiki Project Med Translation Dashboard
                    <!-- <h3><b>Wiki Project Med Translation Dashboard</b></h3>-->
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="main-navigation">
                <ul class="nav navbar-nav navbar-left">
                    <li><a href="leaderboard.php"><span style="font-size:16px">Leaderboard</span></a></li>
                    <li><a href="missing.php"><span style="font-size:16px">Missing</span></a></li>
                </ul>
<?php
//--------------------
require ('login2.php');
//--------------------
$SCRIPT_NAME = htmlspecialchars( $_SERVER['SCRIPT_NAME'] ) ; 
//--------------------
$logout_line = '
    <div class="form-group navbar-form navbar-right">
        <label>You are logged in as: ' . $username . '</label>
        <a class="btn btn-default logged-in form-control" id="logout-btn"  href="index.php?action=logout">
            <span class="glyphicon glyphicon-log-out"></span> Logout
        </a>
    </div>
';
//--------------------<a class='w3-button w3-round-large w3-blue' href='translate.php?title=$title2&code=$code'>Translate</a>
$login_line = "
    <div class='form-group navbar-form navbar-right'>
        <a class='btn btn-default navbar-right' id='login-btn' href='login2.php?action=login'><span class='glyphicon glyphicon-log-in'></span> Login </a>
    </div>";
//--------------------
/*
$login_line222 = "<form class='navbar-form navbar-right' method='submit' action='login2.php?action=login'>
    <button class='btn btn-default' id='login-btn'>
        <span class='glyphicon glyphicon-log-in'></span> Login </button>
</form>";
*/
//--------------------
//print "<li><a href='$SCRIPT_NAME?action=identify'>identify</a></li>";
if ( $username != '' ) {
    print $logout_line;
} else {
    print $login_line;
};

?>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</header>

<main id="body">