<?php
namespace OAuth\Helps;
/*
Usage:
use function OAuth\Helps\add_to_cookie;
use function OAuth\Helps\get_from_cookie;
use function OAuth\Helps\add_access_to_db;
use function OAuth\Helps\get_access_from_db;
*/
include_once __DIR__ . '/../vendor_load.php';
include_once __DIR__ . '/../actions/mdwiki_sql.php';
include_once __DIR__ . '/config.php';

use Defuse\Crypto\Crypto;
use function Actions\MdwikiSql\execute_query;
use function Actions\MdwikiSql\fetch_query;

function decode_value($value)
{
    global $cookie_key;
    try {
        $value = Crypto::decrypt($value, $cookie_key);
    } catch (Exception $e) {
        $value = $value;
    }
    return $value;
}

function encode_value($value)
{
    global $cookie_key;
    try {
        $value = Crypto::encrypt($value, $cookie_key);
    } catch (Exception $e) {
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
    return $value;
}

function add_access_to_db($user, $access_key, $access_secret)
{
    $t = [
        trim($user),
        encode_value($access_key),
        encode_value($access_secret)
    ];
    //---
    $query = <<<SQL
        INSERT INTO access_keys (user_name, access_key, access_secret)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
            access_key = VALUES(access_key),
            access_secret = VALUES(access_secret);
    SQL;
    //---
    execute_query($query, $t);
};

function get_access_from_db($user)
{
    // تأكد من تنسيق اسم المستخدم
    $user = trim($user);

    // SQL للاستعلام عن access_key و access_secret بناءً على اسم المستخدم
    $query = <<<SQL
        SELECT access_key, access_secret
        FROM access_keys
        WHERE user_name = ?;
    SQL;

    // تنفيذ الاستعلام وتمرير اسم المستخدم كمعامل
    $result = fetch_query($query, [$user]);

    // التحقق مما إذا كان قد تم العثور على نتائج
    if ($result) {
        $result = $result[0];
        return [
            'access_key' => decode_value($result['access_key']),
            'access_secret' => decode_value($result['access_secret'])
        ];
    } else {
        // إذا لم يتم العثور على نتيجة، إرجاع null أو يمكنك تخصيص رد معين
        return null;
    }
}
