<!DOCTYPE html>
<HTML lang=en dir=ltr xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
    <meta name="robots" content="noindex">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wiki Project Med Translation Dashboard</title>
    <script src="sorttable.js"></script>
    <!-- <link href="dashboard.css" rel="stylesheet"> -->

<?php 
    //----------------
    // require('login5.php');
    include_once('login5.php');
    //---------------- 
    $old = '
    <script src="https://tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="/bootstrap1.min.js"></script>
    <link href="../bootstrap.min.css" rel="stylesheet">';
    //---------------- 
    $new = '
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    ';
    //---------------- 
    if ( $_SERVER['SERVER_NAME'] == 'localhost' ) { 
        echo $new;
    } else {
        echo ' 
    <script src="https://tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
    ';
    };
    //----------------
    echo '<span id="myusername" style="display:none">' . $username . '</span>';
    //----------------
?>
    <style>
        .alignleft{text-align:left; vertical-align: middle}
        .alignright{text-align:right; vertical-align: middle}
        .aligncenter{text-align:center; vertical-align: middle}
        body {
            padding-bottom:10px;
            padding-top:10px;
            padding-right:30px;
            padding-left:30px;
        }
        .menu {
            font-size: 100%;
            letter-spacing: 0.04em;
            font-weight: bold;
            padding: 0px 0px 0px 0px;
            height: 20px;
            white-space: nowrap;
            text-align: center;
        }
        .menu_item {
            padding: 0px 0px;
            margin: 2px 20px;
            line-height: 20px;
            font-weight: bold;
            border-radius: 2px;
        }

        .colsm5{
            text-align: center;
            position: relative;
            min-height: 1px;
            padding-right: 1px;
            padding-left: 1px
        }
        .mainindex{
			margin-right:20px;
			margin-left:20px;
		}
        .medlogo {
            width: 200px;
            height: auto;
        }
		.spannowrap {
			white-space: nowrap;
		}
        @media (max-width:768px) {
			.spannowrap { 
                white-space: normal;
            }
			.mainindex{
				margin-right:5px;
				margin-left:5px;
			}
            .medlogo {
                width: 100px;
                height: auto;
                float: right;
            }
            body {
                padding-right:10px;
                padding-left:10px;
            }
            .container {
                padding-right:5px;
                padding-left:5px;
            }
            .panel-body {
                padding: 5px;
            }
            .colsm5{
                float: left
            }
			
			.menu_item2 {
				display: inline-block;
				padding: 0px 0px;
				margin: 2px 5px;
				line-height: 20px;
				font-weight: bold;
				background-color: #fff;
				border-radius: 2px;
			}
        }

    </style>
</head>

<body>
<?php
    if ($username == '') {
        echo "
    <form method='GET' action='login5.php' class='form-inline'>
";
    }
?>
    <header>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
			    <div class="navbar-header">
                    <a class="navbar-brand" href='index.php' style="color:blue;">Wiki Project Med Translation Dashboard</a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navigation" aria-expanded="false" wfd-invisible="true">
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
                        <li>
                            <a href="https://github.com/MrIbrahem/Translation-Dashboard" target="_blank"><span style="font-size:16px">Github</span></a>
                        </li>
                    </ul>
                    <ul class='nav navbar-nav navbar-right'>
                        <li>
                            <div id="logged" class="navbar-text" style="display:none">
                                You are logged in as: <span id="user_name"></span>
                            </div>
                        </li>
                        <li>
                            <div id="loginli" class="navbar-text" >
                                <button id="btnlogin" type='submit' class='btn btn-default' style="padding:2px;border:0px;" name='action' value='login'><span class='glyphicon glyphicon-log-in'></span> Login </button>
                            </div>
                        </li>
                        <li>
                            <div id='logoutli' class="navbar-text" style="display:none">
                                <a id="logout-btn" href="index.php?action=logout">
                                    <span class="glyphicon glyphicon-log-out"></span> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
<main id="body">
    <div class='container'>