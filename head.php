<!DOCTYPE html>
<HTML lang=en dir=ltr data-bs-theme="light" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
	<meta name="robots" content="noindex">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark" />
  
    <meta name="theme-color" content="#111111" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#eeeeee" media="(prefers-color-scheme: dark)" />
	<title>Wiki Project Med Translation Dashboard</title>

<?php
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
$hoste = '';
//---
$hoste = 'https://tools-static.wmflabs.org/cdnjs';
if ( $_SERVER['SERVER_NAME'] == 'localhost' )  $hoste = 'https://cdnjs.cloudflare.com';
//---
if (!isset($_GET['noboot'])) {
    echo <<<HTML

    <link href='/Translation_Dashboard/css/styles.css' rel='stylesheet' type='text/css'>
    <link href='/Translation_Dashboard/css/Responsive_Table.css' rel='stylesheet' type='text/css'>
    <link href='/Translation_Dashboard/css/dashboard_new1.css' rel='stylesheet' type='text/css'>
    <link href='$hoste/ajax/libs/font-awesome/5.15.3/css/all.min.css' rel='stylesheet' type='text/css'>
    <link href='$hoste/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css' rel='stylesheet' type='text/css'>
    <link href='$hoste/ajax/libs/datatables.net-bs5/1.13.5/dataTables.bootstrap5.css' rel='stylesheet' type='text/css'>
    <link href='$hoste/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css' rel='stylesheet' type='text/css'>
    <link href="$hoste/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.css" rel='stylesheet' type='text/css'>

    <style> 
    a {
        text-decoration: none;
    }</style>
    <script src='$hoste/ajax/libs/jquery/3.7.0/jquery.min.js'></script>
    <script src='$hoste/ajax/libs/popper.js/2.11.8/umd/popper.min.js'></script>
    <script src='$hoste/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js'></script>
    <script src='$hoste/ajax/libs/datatables.net/2.1.1/jquery.dataTables.min.js'></script>
    <script src='$hoste/ajax/libs/datatables.net-bs5/1.13.5/dataTables.bootstrap5.min.js'></script>
    <script src='$hoste/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js'></script>
    <script src="$hoste/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js"></script>

    <script type="module" src="/Translation_Dashboard/js/color-modes.js"></script>
    <!-- <script src='/Translation_Dashboard/js/sorttable.js'></script> -->
    <script src='/Translation_Dashboard/js/to.js'></script>
    <script src='/Translation_Dashboard/plugins/chart.js/Chart.min.js'></script>

</head>
HTML;
};
//---