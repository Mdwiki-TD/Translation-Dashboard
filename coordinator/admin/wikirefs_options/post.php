<?php
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
if (isset($_POST['newlang'])) {
    if (count($_POST['newlang']) != null) {
        for($i = 0; $i < count($_POST['newlang']); $i++ ){
            //---
            $lang1  	 = $_POST['newlang'][$i] ?? '';
            $move_dots1  = ($_POST['newmove_dots'][$i] ?? '') == '1' ? 1 : 0;
            $expend1     = ($_POST['newexpend'][$i] ?? '') == '1' ? 1 :0;
            $newadden    = ($_POST['newadden'][$i] ?? '') == '1' ? 1 :0;
            //---
            $lang1 = strtolower($lang1);
            //---
            $tabes[$lang1] = array();
            $tabes[$lang1]['move_dots'] = $move_dots1;
            $tabes[$lang1]['expend'] = $expend1;
            $tabes[$lang1]['add_en_lng'] = $newadden;
            //---
        };
        //---
    };
};
//---
$keys_to_add = array('move_dots', 'expend', 'add_en_lng');
//---
if (isset($_POST['lang'])) {
    if (count($_POST['lang']) != null) {
        for($io = 0; $io < count($_POST['lang']); $io++ ){
            //---
            $lang = strtolower($_POST['lang'][$io]);
            //---
            $tabes[$lang] = array();
            foreach ($keys_to_add as $key) {
                $tabes[$lang][$key] = 0;
            };
            //---
        };
        //---
    };
};
//---
function add_key_from_post($key) {
    global $tabes;
    //---
    if (isset($_POST[$key])) {
        if (count($_POST[$key]) != null) {
            for($io = 0; $io < count($_POST[$key]); $io++ ){
                //---
                $vav = strtolower($_POST[$key][$io]);
                //---
                if (!isset($tabes[$vav])) $tabes[$vav] = array();
                $tabes[$vav][$key] = 1;
                //---
            };
        };
    };
};
//---
foreach ($keys_to_add as $key) {
    add_key_from_post($key);
};
//---
// if (isset($_POST['del'])) {
//     for($i = 0; $i < count($_POST['del']); $i++ ) {
//         $key_to_del	= $_POST['del'][$i];
//         //---
//         if (isset($tabes[$key_to_del])) unset($tabes[$key_to_del]);
//     };
// };
//---
if (isset($_POST['lang']) || isset($_POST['newlang'])) {
    //---
    $tabes2 = $tabes;
    //---
    foreach ( $tabes AS $lang => $tab ) {
        foreach ($keys_to_add as $key) {
            if (!isset($tabes2[$lang][$key])) $tabes2[$lang][$key] = 0;
        };
    };
    //---
    set_configs_all_file('fixwikirefs.json', $tabes2);
};
//---
?>