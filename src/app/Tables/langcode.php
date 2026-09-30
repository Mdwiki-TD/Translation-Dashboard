<?php

namespace App\Tables\Langs;


/*
"gswsgs": "bat-smg",
"fiu-vro": "vro",
"roa-rup": "rup",
"lzh": "zh-classical",
"nan": "zh-min-nan",
"yue": "zh-yue",
"be-x-old": "be-tarask",
s
https://ar.wikipedia.org/w/api.php?action=query&format=json&meta=siteinfo&utf8=1&formatversion=2&siprop=languages&sifilteriw=local&sishowalldb=1

https://cxserver.wikimedia.org/v2/list/languagepairs

https://www.wikidata.org/w/api.php?action=query&format=json&meta=languageinfo&utf8=1&formatversion=2&liprop=code%7Cname%7Cfallbacks%7Cvariantnames%7Cvariants%7Cdir%7Cbcp47%7Cautonym

https://db-names.toolforge.org/

"als" : "gsw"
"bat-smg" : "sgs"
"be-x-old" : "be-tarask"
"cbk-zam" : "cbk-x-zam"
"fiu-vro" : "vro"
"map-bms" : "jv-x-bms"
"roa-rup" : "rup"
"roa-tara" : "nap-x-tara"
"nds-nl" : "nds-NL"
"zh-classical" : "lzh"
"zh-min-nan" : "nan"
"zh-yue" : "yue"
*/

use App\SQLorAPI\TitlesTable;

class LangsTables
{
    public static $already_loaded = false;
    public static $L_skip_codes = ["commons", "species", "ary", "arz", "meta", "en", "simple"];
    public static $LChangeCodes = [
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
    public static $L_code_to_lang_name = [];
    public static $L_lang_to_code = [];
    public static $LCodeToLang = [];

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

    public static function get_lang_title($lang_code)
    {
        self::load();
        return self::$LCodeToLang[$lang_code] ?? null;
    }

    public static function get_lang_name($code)
    {
        self::load();
        return self::$L_code_to_lang_name[$code] ?? null;
    }


    public static function get_lang_code($lang_title)
    {
        self::load();
        return self::$L_lang_to_code[$lang_title] ?? null;
    }
}
