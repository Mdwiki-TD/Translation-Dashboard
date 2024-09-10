<?php

namespace Publish\AddToDb;
/*

use function Publish\AddToDb\InsertPageTarget;
use function Publish\AddToDb\retrieveCampaignCategories;
*/

include_once __DIR__ . '/../vendor_load.php';
include_once __DIR__ . '/../Tables/tables.php';
include_once __DIR__ . '/../actions/functions.php';
include_once __DIR__ . '/../actions/mdwiki_sql.php';

use function Actions\MdwikiSql\fetch_query;
use function Actions\MdwikiSql\execute_query;

function retrieveCampaignCategories()
{
    $camp_to_cats = [];
    foreach (fetch_query('select id, category, category2, campaign, depth, def from categories;') as $k => $tab) {
        $camp_to_cats[$tab['campaign']] = $tab['category'];
    };
    return $camp_to_cats;
}

function find_exists($title, $lang, $user)
{
    $query = <<<SQL
        SELECT 1 FROM (
            SELECT 1 FROM pages WHERE title = ? AND lang = ? AND user = ?
            UNION
            SELECT 1 FROM pages_users WHERE title = ? AND lang = ? AND user = ?
        ) AS combined
    SQL;
    // ---
    $params = [$title, $lang, $user, $title, $lang, $user];
    // ---
    $result = fetch_query($query, $params);
    // ---
    return count($result) > 0;
}

function InsertPageTarget($title, $tr_type, $cat, $lang, $user, $test, $target)
{
    global $Words_table;
    // ---
    if (empty($user)) {
        return;
    }
    // ---
    $exists = find_exists($title, $lang, $user);
    // ---
    if ($exists) {
        return;
    }
    // ---
    $word = $Words_table[$title] ?? 0;
    // ---
    $use_user_sql = false;
    // ---
    $target = str_replace("_", " ", $target);
    $user   = str_replace("_", " ", $user);
    // ---
    // if target contains user
    if (strpos($target, $user) !== false) {
        $use_user_sql = true;
    }
    // ---
    // today date like: 2024-08-21
    $today = date("Y-m-d");
    // ---
    $query_user = <<<SQL
        INSERT INTO pages_users (title, lang, user, pupdate, target, add_date)
        SELECT ?, ?, ?, ?, ?, now()
    SQL;
    // ---
    $query_user_params = [$title, $lang, $user, $today, $target];
    // ---
    $query = <<<SQL
        INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
        SELECT ?, ?, ?, ?, ?, now(), ?, ?, ?, now()
    SQL;
    // ---
    $params = [
        $title, $word, $tr_type, $cat, $lang,
        $user,
        $today,
        $target
    ];
    // ---
    // if $title has $user in it then use $query_user else use $query
    if ($use_user_sql) {
        $query = $query_user;
        $params = $query_user_params;
    }
    // ---
    if (!empty($test)) {
        echo "<br>$query<br>";
    }
    execute_query($query, $params = $params);
}
