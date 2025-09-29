<?PHP
//---
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
include_once __DIR__ . '/../include_all.php';
include_once __DIR__ . '/../header.php';
//---
include_once __DIR__ . '/../backend/loaders/load_request.php';
//---
use Tables\SqlTables\TablesSql;
use function Results\ResultsIndex\Results_tables;
use function TD\Render\Forms\print_form_start1;
//---
$tab_titles = [
    "Acetylsalicylic acid/simvastatin/ramipril/atenolol/hydrochlorothiazide",
    "Cochlear implant",
    "Ear foreign body",
    "Earwax",
    "Eustachian tube dysfunction",
    "Hearing loss",
    "Labyrinthitis",
    "Ménière's disease",
    "Nystagmus",
    "Occupational hearing loss",
    "Otitis externa",
    "Otitis media",
    "Perforated eardrum",
    "Perichondritis of the ear",
    "Tinnitus",
];
//---
$tab_titles = [
    "Universal neonatal hearing screening",
    "Gout",
    "WHO AWaRe",
    "Vertigo",
    "Vestibular schwannoma"
];
// ---
// "exists": { "Video:Cancer": { "via": "td", "target": "Video:Cancer" } }
// ---
$exists_list = [
    "Video:Cancer" => [
        "via" => "td",
        "target" => "Video:Cancer"
    ],
    "Universal neonatal hearing screening" => [
        "via" => "before",
        "target" => "الفحص السمعي الشامل لحديثي الولادة"
    ],
    "Gout" => [
        "via" => "before",
        "target" => "نقرس"
    ],
    "WHO AWaRe" => [
        "via" => "before",
        "target" => "تصنيف منظمة الصحة العالمية للمضادات الحيوية"
    ],
    "Vertigo" => [
        "via" => "before",
        "target" => "دوار (عرض)"
    ],
    "Vestibular schwannoma" => [
        "via" => "before",
        "target" => "ورم العصب السمعي"
    ]
];
// ---
$results_list = [
    "inprocess" => array_keys($exists_list),
    "exists" => $exists_list,
    "missing" => array_keys($exists_list),
    "ix" => ""
];
//---
$cat   = "cat";
$camp  = "test";
$test     = $_GET['test'] ?? "";
//---
$code     = $_GET['code'] ?? "ar";
$tra_type = $_GET['tra_type'] ?? "lead";
//---
$global_username = $_GET['global_username'] ?? $GLOBALS['global_username'] ?? "";
$show_exists = $_GET['show_exists'] ?? "";
// ---
$translation_button = $_GET['translation_button'] ?? "";
$full_tr_user = $_GET['full_tr_user'] ?? "";
$allow_whole_translate = $_GET['allow_whole_translate'] ?? '';
// ---
$Lang_tables = [
    "aa" => [
        "code" => "aa",
        "autonym" => "Qafár af",
    ],
    "ab" => [
        "code" => "ab",
        "autonym" => "аԥсшәа",
    ],
    "ace" => [
        "code" => "ace",
        "autonym" => "Acèh",
    ],
    "ar" => [
        "code" => "ar",
        "autonym" => "العربية",
    ]
];
// ---
$code_lang_name = $Lang_tables[$code]['autonym'] ?? "!";
// ---
?>
<div class='container-fluid'>
    <div class='card'>
        <div class='card-body mb-0'>
            <form method='GET' action='test_results.php' class='form-inline'>
                <div class="container">
                    <div class="row row-cols-4">
                        <div class="col">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">code</span>
                                </div>
                                <input class="form-control" type="text" id="code" name="code" value="<?= $code ?>">
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">global username</span>
                                </div>
                                <input class="form-control" type="text" id="global_username" name="global_username" value="<?= $global_username ?>">
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">tra_type</span>
                                </div>
                                <input class="form-control" type="text" id="tra_type" name="tra_type" value="<?= $tra_type ?>">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_exists" name="show_exists" value="1"
                                    <?= (!empty($show_exists)) ? "checked" : ""; ?>>
                                <label class="check-label" for="show_exists">show_exists</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="translation_button" name="translation_button" value="1"
                                    <?= (!empty($translation_button)) ? "checked" : ""; ?>>
                                <label class="check-label" for="translation_button">translation_button</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="full_tr_user" name="full_tr_user" value="1"
                                    <?= (!empty($full_tr_user)) ? "checked" : ""; ?>>
                                <label class="check-label" for="full_tr_user">full_tr_user</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="allow_whole_translate" name="allow_whole_translate" value="1"
                                    <?= (!empty($allow_whole_translate)) ? "checked" : ""; ?>>
                                <label class="check-label" for="allow_whole_translate">allow_whole_translate</label>
                            </div>
                        </div>
                    </div>
                </div>
                <h4 class="aligncenter">
                    <input class="btn btn-outline-primary" type="submit" value="send">
                </h4>
            </form>
        </div>
    </div>
</div>
<?php
//---
echo "<div class='container-fluid'>";
//---
echo Results_tables($code, $camp, $cat, $tra_type, $code_lang_name, $global_username, $results_list, $show_exists, $translation_button, $full_tr_user, $test);
//---
echo "</div>";
//---
echo "<br>";
//---
include_once __DIR__ . '/../footer.php';
//---
