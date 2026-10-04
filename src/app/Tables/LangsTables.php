<?php

namespace App\Tables;

use App\SQLorAPI\TitlesTable;

class LangsTables
{
    private static bool $already_loaded = false;
    public static array $L_skip_codes = ["commons", "species", "ary", "arz", "meta", "en", "simple"];
    private static array $LChangeCodes = [
        "gsw" => "als",
        "sgs" => "bat-smg",
        "nb"    =>    "no",
        "bat_smg"    =>    "bat-smg",
        "be-x-old"    =>    "be-tarask",
        "be_x_old"    =>    "be-tarask",
        "cbk_zam"    =>    "cbk-zam",
        "vro"    =>    "fiu-vro",
        "fiu_vro"    =>    "fiu-vro",
        "map_bms"    =>    "map-bms",
        "nds_nl"    =>    "nds-nl",
        "roa_rup"    =>    "roa-rup",
        "zh_classical"    =>    "zh-classical",
        "zh_min_nan"    =>    "zh-min-nan",
        "zh_yue"    =>    "zh-yue",
        "yue"    =>    "zh-yue",
    ];
    private static array $L_code_to_lang_name = [];
    private static array $L_lang_to_code = [];
    private static array $LCodeToLang = [];

    private static function load(): void
    {
        if (self::$already_loaded) {
            return;
        }
        self::$already_loaded = true;

        $langs_table = (TitlesTable::getInstance())->getLangs();

        foreach ($langs_table as $_ => $langTab) {
            $langCode = $langTab['code'] ?? "";
            $langName = $langTab['autonym'] ?? "";

            if (empty($langCode)) continue;
            if (isset(self::$LChangeCodes[$langCode]) && isset(self::$LCodeToLang[self::$LChangeCodes[$langCode]])) {
                continue;
            }

            $langTitle = "($langCode) $langName";

            self::$LCodeToLang[$langCode] = $langTitle;
            self::$L_code_to_lang_name[$langCode] = $langName;
            self::$L_lang_to_code[$langTitle] = $langCode;
        }
    }

    public static function get_lang_title(string $lang_code): ?string
    {
        self::load();
        return self::$LCodeToLang[$lang_code] ?? null;
    }

    public static function get_lang_name(string $code): ?string
    {
        self::load();
        return self::$L_code_to_lang_name[$code] ?? null;
    }

    public static function get_lang_code(string $lang_title): ?string
    {
        self::load();
        return self::$L_lang_to_code[$lang_title] ?? null;
    }
}
