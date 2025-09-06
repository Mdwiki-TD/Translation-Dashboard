<?php

namespace TranslateMed\Inserter;

/*
Usage:
include_once __DIR__ . '/db_insert.php';

use function TranslateMed\Inserter\insertPage;
use function TranslateMed\Inserter\insertPage_inprocess;

*/

include_once __DIR__ . '/../actions/test_print.php';
include_once __DIR__ . '/../actions/mdwiki_sql.php';

use function Actions\TestPrint\test_print;
use function Actions\MdwikiSql\execute_query;

function insertPage($title_o, $word, $tr_type, $cat, $coden, $useree)
{
    // if (empty($useree) || $useree == "Mr. Ibrahem" || $useree == "MdWikiBot") { return; }
    // ---
    $quae_new = <<<SQL
        INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
        SELECT ?, ?, ?, ?, ?, DATE(NOW()), ?, '', '', DATE(NOW())
        WHERE NOT EXISTS
            (SELECT 1
            FROM pages
            WHERE title = ?
            AND lang = ?
            AND user = ?
        )
    SQL;
    // ---
    test_print($quae_new);
    // ---
    $params = [$title_o, $word, $tr_type, $cat, $coden, $useree, $title_o, $coden, $useree];
    execute_query($quae_new, $params = $params);
};

function insertPage_inprocess($title, $word, $tr_type, $cat, $lang, $user)
{
    // ---
    // title, user, lang, cat, translate_type, word, add_date
    // ---
    $quae_new = <<<SQL
        INSERT INTO in_process (title, user, lang, cat, translate_type, word, add_date)
        SELECT ?, ?, ?, ?, ?, ?, DATE(NOW())
        WHERE NOT EXISTS
            (SELECT 1
            FROM in_process
            WHERE title = ?
            AND lang = ?
            AND user = ?
        )
    SQL;
    // ---
    test_print($quae_new);
    // ---
    $params = [$title, $user, $lang, $cat, $tr_type, $word, $title, $lang, $user];
    execute_query($quae_new, $params = $params);
};
