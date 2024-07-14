<?php

use Defuse\Crypto\Crypto;

function add_to_cookie($key, $value)
{
    global $cookie_key;
    $twoYears = time() + 60 * 60 * 24 * 365 * 2;
    // try {
    //     $value = Crypto::encrypt($value, $cookie_key);
    // } catch (Exception $e) {
    //     $value = $value;
    // };
    setcookie(
        $key,
        $value,
        $twoYears,
        "/",
        "mdwiki.toolforge.org",
        true,  // only secure (https)
        true   // httponly
    );
}

function get_from_cookie($key)
{
    global $cookie_key;
    if (isset($_COOKIE[$key])) {
        try {
            return $_COOKIE[$key];
            // return Crypto::decrypt($_COOKIE[$key], $cookie_key);
        } catch (Exception $e) {
            return '';
        };
    }
    return '';
}
