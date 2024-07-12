<?php
// fixtext.php

if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

include_once 'fix_images.php';
include_once 'fix_cats.php';
include_once 'fix_temps.php';
include_once 'fix_langs_links.php';
include_once 'del_mt_refs.php';
include_once 'expend_refs.php';
include_once 'ref_work.php';

function text_changes_work($text, $allText, $expend_refs = false)
{
    // ---
    if ($expend_refs) {
        $text = refs_expend_work($text, $allText);
    };
    // ---
    $text = remove_bad_refs($text);
    // ---
    $text = del_empty_refs($text);
    // ---
    $text = remove_templates($text);
    // ---
    $text = remove_lang_links($text);
    // ---
    $text = remove_images($text);
    // ---
    $text = remove_categories($text);
    // ---
    return $text;
}
