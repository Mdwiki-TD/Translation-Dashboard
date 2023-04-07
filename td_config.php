<?php
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
/*
include_once('td_config.php');
$ini = Read_ini_file('OAuthConfig.ini');
//---
$ini = Read_ini_file('my_config.ini');
//---
*/
//---
$_dir = "I:/mdwiki/confs/";
//---
if ( strpos( __file__ , '/mnt/' ) === 0 ) $_dir = "/data/project/mdwiki/confs/";
//---
$inio = array();
//---
function Read_ini_file($file) {
    global $_dir;
    //---
    $inio = parse_ini_file( $_dir . $file );
    //---
    return $inio;
};
//---
function get_configs($fileo) {
    //---
    global $_dir;
    //---
    $file = $_dir . $fileo;
    //---
    if(!is_file($file))  file_put_contents($file, '{}');
    //---
    $pv_file = file_get_contents($file);
    //---
    $uu = json_decode( $pv_file, true) ;
    return $uu;
};
//---
function set_configs($file, $key, $value) {
    //---
    global $_dir;
    //---
    $pv_file = file_get_contents($_dir . $file);
    $uu = json_decode( $pv_file, true) ;
    //---
    $uu[$key] = $value;
    //---
    // save the file
    file_put_contents($_dir . $file, json_encode($uu));
};
//---
function set_configs_all_file($file, $contact) {
    //---
    global $_dir;
    //---
    file_put_contents($_dir . $file, json_encode($contact, JSON_PRETTY_PRINT));
};
//---
?>