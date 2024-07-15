<?php
include_once __DIR__ . '/../vendor_load.php';
include_once __DIR__ . '/config.php';

use Defuse\Crypto\Crypto;

function add_to_cookie($key, $value)
{
    global $cookie_key, $domain;
    $twoYears = time() + 60 * 60 * 24 * 365 * 2;
    $secure = ($_SERVER['SERVER_NAME'] == "localhost") ? false : true;
    try {
        $value = Crypto::encrypt($value, $cookie_key);

    } catch (Exception $e) {
        $value = $value;
    };
    echo "add_to_cookie: value: $value<br>";
    setcookie(
        $key,
        $value,
        $twoYears,
        "/",
        $domain,// "mdwiki.toolforge.org",
        $secure,  // only secure (https)
        $secure   // httponly
    );
}

function get_from_cookie($key)
{
    global $cookie_key;
    if (isset($_COOKIE[$key])) {
        try {
            return Crypto::decrypt($_COOKIE[$key], $cookie_key);
        } catch (Exception $e) {
            return $_COOKIE[$key];
            // return '';
        };
    } else {
        // echo "key: $key<br>";
    };
    return '';
}
