<?PHP
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
require('header.php');
require('langcode.php');
require('tables.php');
include_once('functions.php');
//---
//---
$cat_titles = array();
$cat_to_camp = array();
$camp_to_cat = array();
//---
foreach ( quary2('select id, category, display, depth from categories;') AS $k => $tab ) {
    if ($tab['category'] != '' && $tab['display'] != '') {
        $cat_titles[] = $tab['display'];
        $cat_to_camp[$tab['category']] = $tab['display'];
        $camp_to_cat[$tab['display']] = $tab['category']; 
    };
};
//---
//---
$users  = isset($_REQUEST['user']) ? $_REQUEST['user'] : '';
$langs  = isset($_REQUEST['langcode']) ? $_REQUEST['langcode'] : '';
//---
if ($users != '') {
    require('leaderboard/users.php');
} elseif ($langs != '') {
    require('leaderboard/langs.php');
} else {
    require('leader_tables.php');
	require('leaderboard/index.php');
};
//---
require('foter.php');
//---
?>