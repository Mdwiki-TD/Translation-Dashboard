<?php
include_once __DIR__ . '/../WikiParse/Category.php';

use function WikiParse\Category\get_categories;

function remove_categories($text)
{
    // ---
    $categories = get_categories($text);
    // ---
    foreach ($categories as $name => $cat) {
        // echo "delete category: " . $name . "<br>";
        $text = str_replace($cat, '', $text);
    }
    // ---
    return $text;
}
