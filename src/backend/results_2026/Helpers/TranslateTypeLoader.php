<?php

namespace Results\GetResults2026\Helpers;

use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;

function load_translate_type($ty)
{
    static $fullTranslates = [];
    static $noLeadTranslates = [];

    if (empty($fullTranslates)) {
        $rows = get_td_or_sql_translate_type();

        foreach ($rows as $k => $tab) {
            // if tt_full == 1 then add tt_title to $fullTranslates
            if ($tab['tt_full'] == 1) {
                $fullTranslates[] = $tab['tt_title'];
            }
            if ($tab['tt_lead'] == 0) {
                $noLeadTranslates[] = $tab['tt_title'];
            }
        }
    }

    $tab = ($ty == 'full') ? $fullTranslates : $noLeadTranslates;

    return $tab;
}
