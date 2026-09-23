<?php

namespace TD\Render;
use User\CurrentUser;

function admin_text($text)
{
    $currentUser = CurrentUser::getInstance();
    $global_username = $currentUser->getUsername();
    $is_ibrahem = $global_username === "Mr. Ibrahem";

    if (!$is_ibrahem) {
        return "";
    }
    return $text;
}
