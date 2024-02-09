<?php

// Enable error reporting if requested
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Initialize variables for clarity
$keysToAdd = ['move_dots', 'expend', 'add_en_lng'];

$tabes = [];
$tabes = get_configs('fixwikirefs.json');

// Handle new languages
if (isset($_POST['newlang'])) {
    if (count($_POST['newlang']) != null) {
        for ($i = 0; $i < count($_POST['newlang']); $i++) {
            $lang = $_POST['newlang'][$i] ?? '';
            $lang = strtolower($lang);
            $tabes[$lang] = [
                'move_dots' => ($_POST['newmove_dots'][$i] ?? '') == '1' ? 1 : 0,
                'expend' => ($_POST['newexpend'][$i] ?? '') == '1' ? 1 : 0,
                'add_en_lng' => ($_POST['newadden'][$i] ?? '') == '1' ? 1 : 0,
            ];
        }
    }
}

// Handle existing languages
if (isset($_POST['lang'])) {
    if (count($_POST['lang']) != null) {
        for ($io = 0; $io < count($_POST['lang']); $io++) {
            $lang = strtolower($_POST['lang'][$io]);
            $tabes[$lang] = array();
            foreach ($keysToAdd as $key) {
                $tabes[$lang][$key] = 0;
            }
        }
    }
}

// Combine language processing into a single function
function addKeyFromPost($key)
{
    global $tabes;
    if (isset($_POST[$key])) {
        if (count($_POST[$key]) != null) {
            for ($io = 0; $io < count($_POST[$key]); $io++) {
                $vav = strtolower($_POST[$key][$io]);
                if (!isset($tabes[$vav])) $tabes[$vav] = array();
                $tabes[$vav][$key] = 1;
            }
        }
    }
}

// Process additional keys
foreach ($keysToAdd as $key) {
    addKeyFromPost($key);
}

// Uncomment when deletion functionality is needed
if (isset($_POST['del'])) {
    for($i = 0; $i < count($_POST['del']); $i++ ) {
        $key_to_del	= $_POST['del'][$i];
        if (isset($tabes[$key_to_del])) unset($tabes[$key_to_del]);
    }
}

// Save configuration if changes were made
if (isset($_POST['lang']) || isset($_POST['newlang'])) {
    $tabes2 = $tabes;
    foreach ($tabes as $lang => $tab) {
        foreach ($keysToAdd as $key) {
            if (!isset($tabes2[$lang][$key])) $tabes2[$lang][$key] = 0;
        }
    }
    set_configs_all_file('fixwikirefs.json', $tabes2);
}
