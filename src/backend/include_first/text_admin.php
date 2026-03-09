<?php

namespace TD\Render;
/*
Usage:
use function TD\Render\admin_text;
*/

function admin_text($text)
{
    // $user_is_coordinator = (($coordinators[$GLOBALS['global_username']] ?? 0) == 1);
    // ---
    $is_ibrahem = ($GLOBALS['global_username'] ?? "") === "Mr. Ibrahem";
    // ---
    if (!$is_ibrahem) {
        return "";
    }
    return $text;
}
