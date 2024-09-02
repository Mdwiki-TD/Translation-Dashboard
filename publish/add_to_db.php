<?php

namespace Publish\AddToDb;

include_once __DIR__ . '/../vendor_load.php';
include_once __DIR__ . '/../Tables/tables.php';
include_once __DIR__ . '/../actions/functions.php';
include_once __DIR__ . '/../actions/mdwiki_sql.php';

/*

use function Publish\AddToDb\InsertPageTarget;

*/

use function Actions\Functions\escape_string;
use function Actions\MdwikiSql\execute_query;

function InsertPageTarget($title, $tr_type, $cat, $lang, $user, $test, $target)
{
    global $Words_table;
    // ---
    if ($user == "") {
        return;
    }
    // ---
    $word = $Words_table[$title] ?? 0;
    // ---
    $use_user_sql = false;
    // ---
    // if target contains user
    if (strpos($target, $user) !== false) {
        $use_user_sql = true;
    }
    // ---
    $user  = escape_string($user);
    $cat   = escape_string($cat);
    $title = escape_string($title);
    // ---
    // today date like: 2024-08-21
    $today = date("Y-m-d");
    // ---
    $query_user = <<<SQL
        INSERT INTO pages_users (title, lang, user, pupdate, target, add_date)
        SELECT ?, ?, ?, ?, ?, now()
        WHERE NOT EXISTS
            (SELECT 1
            FROM pages
            WHERE title = ?
            AND lang = ?
            AND user = ?
        )
    SQL;
    // ---
    $query_user_params = [$title, $lang, $user, $today, $target, $title, $lang, $user];
    // ---
    $query_pages = <<<SQL
        INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
        SELECT ?, ?, ?, ?, ?, now(), ?, ?, ?, now()
        WHERE NOT EXISTS
            (SELECT 1
            FROM pages
            WHERE title = ?
            AND lang = ?
            AND user = ?
        )
    SQL;
    // ---
    $query_pages_params = [
        $title, $word, $tr_type, $cat, $lang,
        $user,
        $today,
        $target,
        $title, $lang, $user
    ];
    // ---
    $query = $query_pages;
    $params = $query_pages_params;
    // ---
    // if $title has $user in it then use $query_user else use $query
    if ($use_user_sql) {
        $query = $query_user;
        $params = $query_user_params;
    }
    // ---
    if ($test != '') {
        echo "<br>$query<br>";
    }
    execute_query($query, $params = $params);
}
