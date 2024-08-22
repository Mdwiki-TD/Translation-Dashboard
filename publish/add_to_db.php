<?php

namespace Publish\AddToDb;

include_once __DIR__ . '/../Tables/tables.php';

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
    $user  = escape_string($user);
    $cat   = escape_string($cat);
    $title = escape_string($title);
    // ---
    $query_user = <<<SQL
        INSERT INTO pages_users (title, lang, user, pupdate, target, add_date)
        SELECT ?, ?, ?, now(), ?, now()
        WHERE NOT EXISTS
        WHERE NOT EXISTS
            (SELECT 1
            FROM pages
            WHERE title = ?
            AND lang = ?
            AND user = ?
        )
    SQL;
    // ---
    $query_user_params = [$title, $lang, $user, $target, $title, $lang, $user];
    // ---
    $query_pages = <<<SQL
        INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
        SELECT ?, ?, ?, ?, ?, now(), ?, now(), ?, now()
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
        $target,
        $title, $lang, $user
    ];
    // ---
    $query = $query_pages;
    $params = $query_pages_params;
    // ---
    // if $title has $user in it then use $query_user else use $query
    if (strpos($title, $user) !== false) {
        $query = $query_user;
        $params = $query_user_params;
    }
    // ---
    if ($test != '') {
        echo "<br>$query<br>";
    }
    execute_query($query, $params = $params);
}
