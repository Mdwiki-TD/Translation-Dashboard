<?php
//---
/*
require('config.php');
$ini = read_ini('OAuthConfig.ini');
//---
require('../config.php');
$ini = read_ini('my_config.ini');
//---
*/
//---
if ($_GET['test'] != '') {
    // echo(__file__);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
$ini = array();
//---
function read_ini($file) {
    // global $ini;
    //---
    // Read the ini file
    $inifile_local = "../$file";
    $inifile_mdwiki = "/data/project/mdwiki/$file";
    //---
    $inifile = $inifile_mdwiki;
    //---
    // $teste = file_get_contents($inifile_mdwiki);
    // if ( $teste != '' ) { 
    if ( strpos( __file__ , '/mnt/' ) === 0 ) {
        $inifile = $inifile_mdwiki;
    } else {
        $inifile = $inifile_local;
    };
    //---
    $ini = parse_ini_file( $inifile );
    //---
    return $ini;
};
//---
?>