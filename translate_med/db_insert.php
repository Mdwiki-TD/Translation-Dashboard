<?php

namespace TranslateMed\Inserter;

/*
Usage:
include_once __DIR__ . '/db_insert.php';

use function TranslateMed\Inserter\insertPage;

*/

use function Actions\TestPrint\test_print;
use function Actions\MdwikiSql\execute_query;

function insertPage($title_o, $word, $tr_type, $cat, $coden, $useree)
{
    // if (empty($useree) || $useree == "Mr. Ibrahem" || $useree == "MdWikiBot") { return; }
    // ---
    $quae_new = <<<SQL
        INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
        SELECT ?, ?, ?, ?, ?, now(), ?, '', '', now()
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
