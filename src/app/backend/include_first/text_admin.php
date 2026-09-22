<?php

namespace TD\Render;


function admin_text($text)
{
    // $user_is_coordinator = (($coordinators[$GLOBALS['global_username']] ?? 0) == 1);

    global $currentUser;
    $username = isset($currentUser) ? $currentUser->getUsername() : ($GLOBALS['global_username'] ?? "");
    $is_ibrahem = $username === "Mr. Ibrahem";

    if (!$is_ibrahem) {
        return "";
    }
    return $text;
}
