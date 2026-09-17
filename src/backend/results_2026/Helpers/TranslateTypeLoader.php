<?php

namespace Results\GetResults2026\Helpers;

use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;

/**
 * Loads and caches lists of titles that require full translation
 * or do not allow lead-only translation.
 */
class TranslateTypeLoader
{
    private static array $fullTranslates = [];
    private static array $noLeadTranslates = [];
    private static bool $loaded = false;

    /**
     * @param string $type  "full" or "no"
     * @return string[]
     */
    public static function load(string $type): array
    {
        if (!self::$loaded) {
            self::loadData();
        }

        return $type === "full" ? self::$fullTranslates : self::$noLeadTranslates;
    }

    private static function loadData(): void
    {
        $rows = get_td_or_sql_translate_type();

        foreach ($rows as $k => $tab) {
            // $normalizedTitle = str_replace("_", " ", $tab["tt_title"] ?? "");
            $normalizedTitle = $tab['tt_title'];
            // if tt_full == 1 then add tt_title to $fullTranslates
            if (($tab["tt_full"] ?? 0) == 1) {
                self::$fullTranslates[] = $normalizedTitle;
            }
            if (($tab["tt_lead"] ?? 1) == 0) {
                self::$noLeadTranslates[] = $normalizedTitle;
            }
        }

        self::$loaded = true;
    }
}
