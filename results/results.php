<?PHP
//---
include_once 'results_table.php';
include_once 'langcode.php';
include_once 'getcats.php';
include_once 'functions.php';
include_once 'tables.php';
include_once 'sql_tables.php';
//---
$doit = isset($_REQUEST['doit']);
//---
$tra_type  = $_REQUEST['type'] ?? '';
//---
$req  = load_request();
$code = $req['code'];
$camp  = $req['camp'];
$cat  = $req['cat'];
$code_lang_name = $req['code_lang_name'];
//---
$translation_button = $settings['translation_button_in_progress_table']['value'] ?? '0';
//---
if (global_username != 'James Heilman' && global_username != 'Mr. Ibrahem') $translation_button = '0';
//---
$depth  = $_REQUEST['depth'] ?? 1;
$depth  = $depth * 1 ;
//---
// $depth  = $catinput_depth[$cat] ?? 1;
$depth  = $camp_input_depth[$camp] ?? 1;
//---
function make_table( $items, $cod, $cat, $camp, $inprocess=false ) {
    global $tra_type, $translation_button;
    //---
    // global $Words_table, $All_Words_table, $Lead_Refs_table, $All_Refs_table;
    // global $enwiki_pageviews_table, $full_translates, $Assessments_fff, $Assessments_table;
    // global $sql_qids;
    //---
    // $words_tab = ($tra_type == 'all') ? $All_Words_table : $Words_table;
    // $ref_tab   = ($tra_type == 'all') ? $All_Refs_table  : $Lead_Refs_table;
    //---
    // $result = make_results_table($items, $cod, $cat, $words_tab, $ref_tab, $Assessments_table, $tra_type, $enwiki_pageviews_table, $translation_button, $sql_qids, $full_translates, $inprocess=$inprocess );
    //---
    $result = make_results_table($items, $cod, $cat, $camp, $tra_type, $translation_button, $inprocess=$inprocess );
    //---
    return $result;
    }
//---
if ( $code_lang_name == '' ) $doit = false;
//---
echo "<div class='container'>";
//---
if ($doit) {
    //---
    $items = get_cat_exists_and_missing($cat, $camp, $depth, $code) ; # mdwiki pages in the cat
    //---
    if ($items == null ) $items = array() ;
    //---
    $len_of_exists_pages = $items['len_of_exists'];
    $items_missing       = $items['missing'];
    //---
    $cat2 = $camps_cat2[$camp] ?? '';
    //---
    test_print("items_missing:" . count($items_missing) . "<br>");
    //---
    if ($cat2 != '') {
        $cat2_members = get_mdwiki_cat_members($cat2, $use_cache = true, $depth = $depth, $camp = $camp);
        // $items_missing2 = array();
        // $items_missing2 = $items_missing titles that is in $cat2_members
        $items_missing2 = array_intersect($items_missing, $cat2_members);

        test_print("items_missing2:" . count($items_missing2) . "<br>");
        $items_missing = $items_missing2;
    }
    //---
    test_print("len_of_exists_pages: $len_of_exists_pages<br>");
    //---
    $missing = array();
    foreach ( $items_missing as $key => $cca ) if (!in_array($cca, $missing)) $missing[] = $cca;
    //---
    $in_process = get_in_process($missing, $code);
    //---
    $len_in_process = count($in_process);
    //---
    $len_of_missing_pages = count($missing);
    $len_of_all           = $len_of_exists_pages + $len_of_missing_pages;
    //---
	$cat2 = "Category:" . str_replace ( 'Category:' , '' , $cat );
	$caturl = "<a href='https://mdwiki.org/wiki/$cat2'>category</a>";
    //---
    $ix =  "Found $len_of_all pages in $caturl, $len_of_exists_pages exists, and $len_of_missing_pages missing in (<a href='https://$code.wikipedia.org'>$code</a>), $len_in_process In process." ;
    //---
    $res_line = " Results ";
    //---
    if (global_test != '') $res_line .= 'test:';
    //---
    // delete $in_process keys from $missing
    if ($len_in_process > 0) {
        $missing = array_diff($missing, array_keys($in_process));
    };
    //---
    if (isset($doit) && global_test != '' ) {
        //---
        echo "_REQUEST code:" . isset($_REQUEST['code']) . "<br>";
        echo "code:$code<br>";
        echo "code_lang_name:$code_lang_name<br>";
        //---
    };
    $table = make_table($missing, $code, $cat, $camp) ;
    //---
    echo <<<HTML
    <br>
    <div class='card'>
        <div class='card-header'>
            <span class='h5'>$res_line:</span> <span class='only_on_mobile'><b>Click the article name to translate</b></span>
            <!-- <h5>$ix</h5> -->
        </div>
        <div class='card-body1 card2'>
            $table
        </div>
    </div>
    HTML;
    //---
    if ($len_in_process > 0) {
        //---
        $table_2 = make_table($in_process, $code, $cat, $camp, $inprocess=true) ;
        //---
        echo <<<HTML
        <br>
        <div class='card'>
            <div class='card-header'>
                <h5>In process ($len_in_process):</h5>
            </div>
            <div class='card-body1 card2'>
                $table_2
            </div>
        </div>
        HTML;
    };
    //---
    echo '</div>';
};
//---
echo "</div>";
//---
?>
