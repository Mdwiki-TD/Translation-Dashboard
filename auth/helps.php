<?php

namespace OAuth\Helps;
/*
Usage:
use function OAuth\Helps\add_to_cookie;
use function OAuth\Helps\get_from_cookie;
*/

include_once __DIR__ . '/../vendor_load.php';
include_once __DIR__ . '/config.php';

use Defuse\Crypto\Crypto;

function decode_value($value)
{
    global $cookie_key;
    try {
        $value = Crypto::decrypt($value, $cookie_key);
    } catch (\Exception $e) {
        $value = $value;
    }
    return $value;
}

function encode_value($value)
{
    global $cookie_key;
    try {
        $value = Crypto::encrypt($value, $cookie_key);
    } catch (\Exception $e) {
        $value = $value;
    };
    return $value;
}
function add_to_cookie($key, $value)
{
    global $domain;
    $twoYears = time() + 60 * 60 * 24 * 365 * 2;
    $secure = ($_SERVER['SERVER_NAME'] == "localhost") ? false : true;

    $value = encode_value($value);

    echo "add_to_cookie: value: $value<br>";
    setcookie(
        $key,
        $value,
        $twoYears,
        "/",
        $domain, // "mdwiki.toolforge.org",
        $secure,  // only secure (https)
        $secure   // httponly
    );
}

function get_from_cookie($key)
{
    if (isset($_COOKIE[$key])) {
        $value = decode_value($_COOKIE[$key]);
    } else {
        // echo "key: $key<br>";
        $value = "";
    };
    if ($key == "username") {
        $value = str_replace("+", " ", $value);
    };
    return $value;
}
