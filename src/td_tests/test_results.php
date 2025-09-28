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
    "Gout",
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
    "Universal neonatal hearing screening",
    "Vertigo",
    "Vestibular schwannoma",
    "WHO AWaRe"
];
// ---
$results_list = [
    "inprocess" => $tab_titles,
    "exists" => $tab_titles,
    "missing" => $tab_titles,
    "ix" => ""
];
//---
$test     = $_GET['test'] ?? "";
$code     = $_GET['code'] ?? "ar";
$tra_type = $_GET['tra_type'] ?? "lead";
//---
$cat   = $_GET['cat'] ?? "cat";
$camp  = $_GET['camp'] ?? "test";
//---
$allow_whole_translate = $_GET['allow_whole_translate'] ?? TablesSql::$s_settings['allow_type_of_translate']['value'] ?? '1';
$global_username = $_GET['global_username'] ?? $GLOBALS['global_username'] ?? "";
// ---
$code_lang_name  = $_GET['code_lang_name'] ?? "";
$show_exists = $_GET['show_exists'] ?? "1";
$translation_button = $_GET['translation_button'] ?? "";
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
$code_lang_name = $Lang_tables[$code]['autonym'] ?? "";
// ---
$form_start1 = print_form_start1($allow_whole_translate, $Lang_tables, TablesSql::$s_campaign_input_list, $cat, $camp, $code_lang_name, $code, $tra_type, $global_username);
// ---
echo <<<HTML
    <div class='container'>
        <div class='card'>
            <div class='card-body mb-0'>
            <div class='mainindex'>
                <form method='GET' action='test_results.php' class='form-inline'>
                    $form_start1
                </form>
            </div>
            </div>
        </div>
    </div>
HTML;
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
