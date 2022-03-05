<!DOCTYPE html>
<HTML lang=en dir=ltr xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
<meta name="robots" content="noindex">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Wiki Project Med Translation Dashboard</title>

<!-- <script type="text/javascript" src="https://tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
<script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

<script src="sorttable.js"></script>

<script src="/bootstrap1.min.js" type="text/javascript"></script>
<!-- <script src="//tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>-->

<link href="dashboard.css" rel="stylesheet">
<!--<link href="/mem.css" rel="stylesheet">-->
<link href="../bootstrap.min.css" rel="stylesheet">
<style>
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
    display: inline-block;
    padding: 0px 0px;
    margin: 2px 5px;
    line-height: 20px;
    font-weight: bold;
    background-color: #fff;
    border-radius: 2px;
}

.colsm4{
    position: relative;
    min-height: 1px;
}

.colsm5{
	position: relative;
	min-height: 1px;
	padding-right: 1px;
	padding-left: 1px
}

@media (min-width:1200px) {
    .colsm4{
      padding-right: 35px;
      padding-left: 35px
    }
	.colsm5 {
    text-align: center
	}
}

@media (min-width:768px) {
    .colsm4{
      float: left;
      width: 33.33333333%
    }
}
@media (max-width:768px) {
    .colsm5{
      float: left
    }
}

</style>
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
                    <li><a href="my.php"><span style="font-size:16px">My Board</span></a></li>
                    <!--<li><a href="calendar.php"><span style="font-size:16px">calendar</span></a></li> -->
                    <li><a href="missing.php"><span style="font-size:16px">Missing</span></a></li>
                    <li><a href="https://github.com/MrIbrahem/Translation-Dashboard" target="_blank"><span style="font-size:16px">Github</span></a></li>
                </ul>