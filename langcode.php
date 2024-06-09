<?PHP
//---
/*
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
// ---
$skip_codes = ["commons", "species", "ary", "arz", "meta"];
//---
$change_codes = [
    "bat_smg"	=>	"bat-smg",
    "be-x-old"	=>	"be-tarask",
    "be_x_old"	=>	"be-tarask",
    "cbk_zam"	=>	"cbk-zam",
    "fiu_vro"	=>	"fiu-vro",
    "map_bms"	=>	"map-bms",
    "nds_nl"	=>	"nds-nl",
    "roa_rup"	=>	"roa-rup",
    "zh_classical"	=>	"zh-classical",
    "zh_min_nan"	=>	"zh-min-nan",
    "zh_yue"	=>	"zh-yue",
];
#---
$code_to_wikiname = [
    "aa"	=>	"Afar",
    "ab"	=>	"Аԥсуа",
    "ace"	=>	"Basa Acèh",
    "ady"	=>	"Адыгэбзэ",
    "af"	=>	"Afrikaans",
    "ak"	=>	"Akana",
    "als"	=>	"Alemannisch",
    "alt"	=>	"Алтай",
    "am"	=>	"አማርኛ",
    "ami"	=>	"Pangcah",
    "an"	=>	"Aragonés",
    "ang"	=>	"Englisc",
    "anp"	=>	"अंगिका",
    "ar"	=>	"العربية",
    "arc"	=>	"ܐܪܡܝܐ",
    "as"	=>	"অসমীয়া",
    "ast"	=>	"Asturianu",
    "atj"	=>	"Atikamekw",
    "av"	=>	"Авар",
    "avk"	=>	"Kotava",
    "awa"	=>	"अवधी",
    "ay"	=>	"Aymar",
    "az"	=>	"Azərbaycanca",
    "azb"	=>	"تۆرکجه",
    "ba"	=>	"Башҡорт",
    "ban"	=>	"Bali",
    "bar"	=>	"Boarisch",
    "bat-smg"	=>	"Žemaitėška",
    "bcl"	=>	"Bikol",
    "be"	=>	"Беларуская",
    "be-tarask"	=>	"Беларуская",
    "bg"	=>	"Български",
    "bh"	=>	"भोजपुरी",
    "bi"	=>	"Bislama",
    "bjn"	=>	"Bahasa Banjar",
    "blk"	=>	"ပအိုဝ်ႏဘာႏသာႏ",
    "bm"	=>	"Bamanankan",
    "bn"	=>	"বাংলা",
    "bo"	=>	"བོད་སྐད",
    "bpy"	=>	"ইমার ঠার/বিষ্ণুপ্রিয়া মণিপুরী",
    "br"	=>	"Brezhoneg",
    "bs"	=>	"Bosanski",
    "bug"	=>	"Basa Ugi",
    "bxr"	=>	"Буряад",
    "ca"	=>	"Català",
    "cbk-zam"	=>	"Chavacano de Zamboanga",
    "cdo"	=>	"Mìng-dĕ̤ng-ngṳ̄",
    "ce"	=>	"Нохчийн",
    "ceb"	=>	"Sinugboanong Binisaya",
    "ch"	=>	"Chamoru",
    "cho"	=>	"Choctaw",
    "chr"	=>	"ᏣᎳᎩ",
    "chy"	=>	"Tsetsêhestâhese",
    "ckb"	=>	"Soranî / کوردی",
    "co"	=>	"Corsu",
    "cr"	=>	"Nehiyaw",
    "crh"	=>	"Qırımtatarca",
    "cs"	=>	"Čeština",
    "csb"	=>	"Kaszëbsczi",
    "cu"	=>	"Словѣньскъ",
    "cv"	=>	"Чăваш",
    "cy"	=>	"Cymraeg",
    "da"	=>	"Dansk",
    "dag"	=>	"dagbanli",
    "de"	=>	"Deutsch",
    "din"	=>	"Thuɔŋjäŋ",
    "diq"	=>	"Zazaki",
    "dsb"	=>	"Dolnoserbski",
    "dty"	=>	"डोटेली",
    "dv"	=>	"ދިވެހިބަސް",
    "dz"	=>	"ཇོང་ཁ",
    "ee"	=>	"Eʋegbe",
    "el"	=>	"Ελληνικά",
    "eml"	=>	"Emiliàn e rumagnòl",
    "en"	=>	"English",
    "eo"	=>	"Esperanto",
    "es"	=>	"Español",
    "et"	=>	"Eesti",
    "eu"	=>	"Euskara",
    "ext"	=>	"Estremeñu",
    "fa"	=>	"فارسی",
    "ff"	=>	"Fulfulde",
    "fi"	=>	"Suomi",
    "fiu-vro"	=>	"Võro",
    "fj"	=>	"Na Vosa Vakaviti",
    "fo"	=>	"Føroyskt",
    "fr"	=>	"Français",
    "frp"	=>	"Arpetan",
    "frr"	=>	"Nordfriisk",
    "fur"	=>	"Furlan",
    "fy"	=>	"Frysk",
    "ga"	=>	"Gaeilge",
    "gag"	=>	"Gagauz",
    "gan"	=>	"贛語",
    "gcr"	=>	"Kriyòl Gwiyannen",
    "gd"	=>	"Gàidhlig",
    "gl"	=>	"Galego",
    "glk"	=>	"گیلکی",
    "gn"	=>	"Avañe'ẽ",
    "gom"	=>	"गोंयची कोंकणी / Gõychi Konknni",
    "gor"	=>	"Hulontalo",
    "got"	=>	"𐌲𐌿𐍄𐌹𐍃𐌺",
    "gu"	=>	"ગુજરાતી",
    "guc"	=>	"wayuunaiki",
    "gur"	=>	"farefare",
    "guw"	=>	"gungbe",
    "gv"	=>	"Gaelg",
    "ha"	=>	"Hausa / هَوُسَ",
    "hak"	=>	"Hak-kâ-fa / 客家話",
    "haw"	=>	"Hawaiʻi",
    "he"	=>	"עברית",
    "hi"	=>	"हिन्दी",
    "hif"	=>	"Fiji Hindi",
    "ho"	=>	"Hiri Motu",
    "hr"	=>	"Hrvatski",
    "hsb"	=>	"Hornjoserbsce",
    "ht"	=>	"Krèyol ayisyen",
    "hu"	=>	"Magyar",
    "hy"	=>	"Հայերեն",
    "hyw"	=>	"Արեւմտահայերէն",
    "hz"	=>	"Otsiherero",
    "ia"	=>	"Interlingua",
    "id"	=>	"Bahasa Indonesia",
    "ie"	=>	"Interlingue",
    "ig"	=>	"Ìgbò",
    "ik"	=>	"Iñupiatun",
    "ilo"	=>	"Ilokano",
    "inh"	=>	"ГӀалгӀай",
    "io"	=>	"Ido",
    "is"	=>	"Íslenska",
    "it"	=>	"Italiano",
    "iu"	=>	"ᐃᓄᒃᑎᑐᑦ",
    "ja"	=>	"日本語",
    "jam"	=>	"Jumiekan Kryuol",
    "jbo"	=>	"Lojban",
    "jv"	=>	"Basa Jawa",
    "ka"	=>	"ქართული",
    "kaa"	=>	"Qaraqalpaqsha",
    "kab"	=>	"Taqbaylit",
    "kbd"	=>	"Адыгэбзэ",
    "kbp"	=>	"Kabɩyɛ",
    "kcg"	=>	"Tyap",
    "kg"	=>	"Kikôngo",
    "ki"	=>	"Gĩkũyũ",
    "kj"	=>	"Kuanyama",
    "kk"	=>	"Қазақша",
    "kl"	=>	"Kalaallisut",
    "km"	=>	"ភាសាខ្មែរ",
    "kn"	=>	"ಕನ್ನಡ",
    "ko"	=>	"한국어",
    "koi"	=>	"Перем Коми",
    "kr"	=>	"Kanuri",
    "krc"	=>	"Къарачай-Малкъар",
    "ks"	=>	"कश्मीरी / كشميري",
    "ksh"	=>	"Ripoarisch",
    "ku"	=>	"Kurdî / كوردی",
    "kv"	=>	"Коми",
    "kw"	=>	"Kernowek/Karnuack",
    "ky"	=>	"Кыргызча",
    "la"	=>	"Latina",
    "lad"	=>	"Dzhudezmo",
    "lb"	=>	"Lëtzebuergesch",
    "lbe"	=>	"Лакку",
    "lez"	=>	"Лезги чІал",
    "lfn"	=>	"Lingua franca nova",
    "lg"	=>	"Luganda",
    "li"	=>	"Limburgs",
    "lij"	=>	"Lìgure",
    "lld"	=>	"Lingaz",
    "lmo"	=>	"Lumbaart",
    "ln"	=>	"Lingala",
    "lo"	=>	"ລາວ",
    "lrc"	=>	"لۊری شومالی",
    "lt"	=>	"Lietuvių",
    "ltg"	=>	"Latgaļu",
    "lv"	=>	"Latviešu",
    "mad"	=>	"Madhurâ",
    "mai"	=>	"मैथिली",
    "map-bms"	=>	"Basa Banyumasan",
    "mdf"	=>	"Мокшень",
    "mg"	=>	"Malagasy",
    "mh"	=>	"Ebon",
    "mhr"	=>	"Олык Марий",
    "mi"	=>	"Māori",
    "min"	=>	"Minangkabau",
    "mk"	=>	"Македонски",
    "ml"	=>	"മലയാളം",
    "mn"	=>	"Монгол",
    "mni"	=>	"ꯃꯤꯇꯩꯂꯣꯟ",
    "mnw"	=>	"မန်",
    "mr"	=>	"मराठी",
    "mrj"	=>	"Кырык Мары",
    "ms"	=>	"Bahasa Melayu",
    "mt"	=>	"Malti",
    "mus"	=>	"Muskogee",
    "mwl"	=>	"Mirandés",
    "my"	=>	"မြန်မာဘာသာ",
    "myv"	=>	"Эрзянь",
    "mzn"	=>	"مَزِروني",
    "na"	=>	"dorerin Naoero",
    "nah"	=>	"Nāhuatl",
    "nap"	=>	"Nnapulitano",
    "nds"	=>	"Plattdüütsch",
    "nds-nl"	=>	"Nedersaksisch",
    "ne"	=>	"नेपाली",
    "new"	=>	"नेपाल भाषा",
    "ng"	=>	"Ndonga",
    "nia"	=>	"Li Niha",
    "nl"	=>	"Nederlands",
    "nn"	=>	"Nynorsk",
    "no"	=>	"Norsk",
    "nov"	=>	"Novial",
    "nqo"	=>	"ߒߞߏ",
    "nrm"	=>	"Nouormand/Normaund",
    "nso"	=>	"Sepedi",
    "nv"	=>	"Diné bizaad",
    "ny"	=>	"Chichewa",
    "oc"	=>	"Occitan",
    "olo"	=>	"Karjalan",
    "om"	=>	"Oromoo",
    "or"	=>	"ଓଡ଼ିଆ",
    "os"	=>	"Иронау",
    "pa"	=>	"ਪੰਜਾਬੀ",
    "pag"	=>	"Pangasinan",
    "pam"	=>	"Kapampangan",
    "pap"	=>	"Papiamentu",
    "pcd"	=>	"Picard",
    "pcm"	=>	"Naijá",
    "pdc"	=>	"Deitsch",
    "pfl"	=>	"Pälzisch",
    "pi"	=>	"पाऴि",
    "pih"	=>	"Norfuk",
    "pl"	=>	"Polski",
    "pms"	=>	"Piemontèis",
    "pnb"	=>	"شاہ مکھی پنجابی",
    "pnt"	=>	"Ποντιακά",
    "ps"	=>	"پښتو",
    "pt"	=>	"Português",
    "pwn"	=>	"pinayuanan",
    "qu"	=>	"Runa Simi",
    "rm"	=>	"Rumantsch",
    "rmy"	=>	"romani - रोमानी",
    "rn"	=>	"Ikirundi",
    "ro"	=>	"Română",
    "roa-rup"	=>	"Armãneashce",
    "roa-tara"	=>	"Tarandíne",
    "ru"	=>	"Русский",
    "rue"	=>	"Русиньскый",
    "rw"	=>	"Ikinyarwanda",
    "sa"	=>	"संस्कृतम्",
    "sah"	=>	"Саха тыла",
    "sat"	=>	"ᱥᱟᱱᱛᱟᱲᱤ",
    "sc"	=>	"Sardu",
    "scn"	=>	"Sicilianu",
    "sco"	=>	"Scots",
    "sd"	=>	"سنڌي، سندھی ، सिन्ध",
    "se"	=>	"Sámegiella",
    "sg"	=>	"Sängö",
    "sh"	=>	"Srpskohrvatski / Српскохрватски",
    "shi"	=>	"Taclḥit",
    "shn"	=>	"လိၵ်ႈတႆး",
    "si"	=>	"සිංහල",
    "simple"	=>	"Simple English",
    "sk"	=>	"Slovenčina",
    "skr"	=>	"سرائیکی",
    "sl"	=>	"Slovenščina",
    "sm"	=>	"Gagana Samoa",
    "smn"	=>	"Anarâškielâ",
    "sn"	=>	"chiShona",
    "so"	=>	"Soomaali",
    "sq"	=>	"Shqip",
    "sr"	=>	"Српски / Srpski",
    "srn"	=>	"Sranantongo",
    "ss"	=>	"SiSwati",
    "st"	=>	"Sesotho",
    "stq"	=>	"Seeltersk",
    "su"	=>	"Basa Sunda",
    "sv"	=>	"Svenska",
    "sw"	=>	"Kiswahili",
    "szl"	=>	"Ślůnski",
    "szy"	=>	"Sakizaya",
    "ta"	=>	"தமிழ்",
    "tay"	=>	"Tayal",
    "tcy"	=>	"ತುಳು",
    "te"	=>	"తెలుగు",
    "tet"	=>	"Tetun",
    "tg"	=>	"Тоҷикӣ",
    "th"	=>	"ไทย",
    "ti"	=>	"ትግርኛ",
    "tk"	=>	"Türkmen",
    "tl"	=>	"Tagalog",
    "tn"	=>	"Setswana",
    "to"	=>	"faka Tonga",
    "tpi"	=>	"Tok Pisin",
    "tr"	=>	"Türkçe",
    "trv"	=>	"Taroko",
    "ts"	=>	"Xitsonga",
    "tt"	=>	"Tatarça / Татарча",
    "tum"	=>	"chiTumbuka",
    "tw"	=>	"Twi",
    "ty"	=>	"Reo Mā`ohi",
    "tyv"	=>	"Тыва",
    "udm"	=>	"Удмурт кыл",
    "ug"	=>	"ئۇيغۇر تىلى",
    "uk"	=>	"Українська",
    "ur"	=>	"اردو",
    "uz"	=>	"O‘zbek",
    "ve"	=>	"Tshivenda",
    "vec"	=>	"Vèneto",
    "vep"	=>	"Vepsän",
    "vi"	=>	"Tiếng Việt",
    "vls"	=>	"West-Vlams",
    "vo"	=>	"Volapük",
    "wa"	=>	"Walon",
    "war"	=>	"Winaray",
    "wo"	=>	"Wolof",
    "wuu"	=>	"吴语",
    "xal"	=>	"Хальмг",
    "xh"	=>	"isiXhosa",
    "xmf"	=>	"მარგალური",
    "yi"	=>	"ייִדיש",
    "yo"	=>	"Yorùbá",
    "za"	=>	"Cuengh",
    "zea"	=>	"Zeêuws",
    "zh"	=>	"中文",
    "zh-classical"	=>	"古文 / 文言文",
    "zh-min-nan"	=>	"Bân-lâm-gú",
    "zh-yue"	=>	"粵語",
    "zu"	=>	"isiZulu",
    ];
//---
$lang_to_code = []; // "(simple) Simple English"	=>	"simple",
//---
$code_to_lang = [];
//---
foreach ( $code_to_wikiname AS $code	=>	$name ) {
    $lang = "($code) $name";
    $code_to_lang[$code] = $lang;
    $lang_to_code[$lang] = $code;
};
?>