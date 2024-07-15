<?php
namespace Translate\Fixes\FixImages;

/*
Usage:

use function Translate\Fixes\FixImages\remove_images;

*/


function remove_images($text)
{
    $pattern = '/\[\[(File:[^][|]+)\|([^][]*(\[\[[^][]+\]\][^][]*)*)\]\]/';
    // ---
    preg_match_all($pattern, $text, $matches);
    // ---
    $images = array();
    // array ( 'File:AwareLogo.png' => '[[File:AwareLogo.png|thumb|upright=1.3|Logo of the WHO Aware Classification]]', )
    // ---
    foreach ($matches[0] as $link) {
        $file_name = $matches[1][array_search($link, $matches[0])];
        // ---
        $new_text = sprintf("{{subst:#ifexist:%s|%s}}", $file_name, $link);
        // ---
        $text = str_replace($link, $new_text, $text);
        // ---
        $images[$file_name] = $link;
        // ---
    }
    // ---
    // echo "<pre>";
    // echo htmlentities(var_export($images, true));
    // echo "</pre><br>";
    // ---
    return $text;
}
