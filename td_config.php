<?php
//---
/*
include_once('td_config.php');
$ini = Read_ini_file('OAuthConfig.ini');
//---
$ini = Read_ini_file('my_config.ini');
//---
*/
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
$inio = array();
//---
function Read_ini_file($file) {
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
    $inio = parse_ini_file( $inifile );
    //---
    return $inio;
};
//---
?>