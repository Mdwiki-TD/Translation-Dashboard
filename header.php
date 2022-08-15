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
    $new= '
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
    ';
    //---------------- 
    if ($_REQUEST['bs5'] != '') {
        $new = '
        <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
        ';
    };
    //---------------- 
    if ( $_SERVER['SERVER_NAME'] == 'localhost' ) { 
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>';
        echo $new;
    } else {
        echo ' 
    <script src="https://tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
    ';
    };
    //----------------
    echo '<span id="myusername" style="display:none">' . $username . '</span>
    ';
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
    <header>
        <nav class="navbar navbar-default">
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
                        <li>
                            <a href="https://github.com/MrIbrahem/Translation-Dashboard" target="_blank"><span style="font-size:16px">Github</span></a>
                        </li>
                    </ul>
                    <ul class='nav navbar-nav navbar-right'>
                        <li>
                            <div id="logged" class="navbar-text" style="display:none">
                                
                            </div>
                        </li>
                        <li>
                            <div id="loginli" class="navbar-text" >
                                <a class='btn btn-default' style="padding:2px;border:0px;" onclick="login()"><span class='glyphicon glyphicon-log-in'></span> Login </a>
                            </div>
                        </li>
                        <li>
                            <div id='logoutli' class="navbar-text" style="display:none">
                                User: <span id="user_name"></span>
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