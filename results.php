<?PHP
//---
include_once('results_table.php');
include_once('tables.php'); 
include_once('langcode.php');
include_once('getcats.php');
include_once('functions.php');
include_once('sql_tables.php');
//---
$doit = isset($_REQUEST['doit']);
//---
$code = $_REQUEST['code'] ?? '';
//---
if ($code == 'undefined') $code = "";
//---
$code = $lang_to_code[$code] ?? $code;
$code_lang_name = $code_to_lang[$code] ?? ''; 
//---
$tra_type  = $_REQUEST['type'] ?? '';
//---
$cat = $_REQUEST['cat'] ?? '';
//---
if ($cat == "undefined") $cat = "RTT";
//---
$translation_button = $settings['translation_button_in_progress_table']['value'] ?? '0';
if (global_username != 'James Heilman' && global_username != 'Mr. Ibrahem') $translation_button = '0';
//---
function make_table( $items, $cod, $cat, $inprocess=false ) {
    global $Words_table, $All_Words_table, $Assessments_table, $tra_type, $Assessments_fff;
    global $Lead_Refs_table, $All_Refs_table, $enwiki_pageviews_table, $translation_button;

    global $sql_qids;
    
    $words_tab = ($tra_type == 'all') ? $All_Words_table : $Words_table;
    $ref_tab   = ($tra_type == 'all') ? $All_Refs_table  : $Lead_Refs_table;
    //---
    $result = make_results_table($items, $cod, $cat, $words_tab, $ref_tab, $Assessments_table, $Assessments_fff, $tra_type, $enwiki_pageviews_table, $translation_button, $sql_qids, $inprocess=$inprocess );
    //---
    return $result;
    }
//---
$doit2 = false ;
//---
if ( $code_lang_name != '' ) $doit2 = true;
//---
echo "<div class='container'>";
//---
if ( $doit && $doit2 ) {
    //---
    if (global_test) echo '$doit and $doit2:<br>';
    //---
    $items = get_cat_members($cat, $depth, $code) ; # mdwiki pages in the cat
    //---
    if ($items == null ) $items = array() ;
    //---
    $len_of_exists_pages = $items['len_of_exists'];
    $items_missing       = $items['missing'];
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
    $table = make_table($missing, $code, $cat) ;
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
        $table_2 = make_table($in_process, $code, $cat, $inprocess=true) ;
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
    if (isset($doit) && global_test != '' ) {
        //---
        echo "_REQUEST code:" . isset($_REQUEST['code']) . "<br>";
        echo "code:$code<br>";
        echo "code_lang_name:$code_lang_name<br>";
        //---
    };
    echo '</div>';
};
//---
echo "</div>";
//---
?>
