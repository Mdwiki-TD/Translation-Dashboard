<?PHP

$prefilled_requests = [] ;

//-----------------------------

function get_request ( $key , $default = "" ) /*:string*/ {
    if ( isset ( $prefilled_requests[$key] ) ) return $prefilled_requests[$key] ;
    if ( isset ( $_REQUEST[$key] ) ) return str_replace ( "\'" , "'" , $_REQUEST[$key] ) ;
    return $default ;
}
//-----------------------------
$code = get_request ( 'code' , '' ) ;
$cat = get_request ( 'cat' , 'RTT' ) ;
$depth = get_request ( 'depth' , '1' ) * 1 ;
$doit = isset ( $_REQUEST['doit'] ) ;
print "
<form method='get' class='form-inline'>
" ;
print "<hr/>
<table class='table-condensed' border=0><tbody>
" ;

#--------------------

print "<tr><td><b>Mdwiki category</b></td><td><input class='span4' type='text' name='cat' id='cat' value='" . htmlspecialchars($cat,ENT_QUOTES) . "' placeholder='Root category' />, depth <input class='span1' name='depth' type='text' value='$depth' /> (optional)</td></tr>
" ;

#--------------------
print "<tr><td nowrap><b>Target language</b></td>
<td style='width:100%'>" ;

#--------------------
require ('langcode.php');
#--------------------
function make_datalist() {
    global $lang_to_code,$code_to_lang;
    //--------------------------
	$caca = strtolower($_GET['code']);
    $coco = $code_to_lang[$caca];
	$coco = assert($coco) ? $coco : $caca;
    //--------------------------
    print "<input list='Languages' class='span2' type='text' placeholder='two letter code' name='code' id='code' value='$coco'>";
    //--------------------------
    print '<datalist id="Languages">';
    //--------------------------
    foreach ( $lang_to_code AS $lange => $cod ) {
        print "<option value='$cod'>$lange</option>";
    };
    //--------------------------
    print '</datalist></input>
    ' ;
};
#--------------------
function make_drop() {
    global $lang_to_code;
    print '<select dir="ltr" id="code" class="form-control custom-select">';
    //--------------------------
	$caca = assert($_GET['code']) ? $_GET['code'] : $_SESSION['code'] ;
    //--------------------------
    foreach ( $lang_to_code AS $lange => $cod ) {
        $cdcdc = $caca == $cod ? "selected" : "";
        print "<option id='$cod' $cdcdc>$lange</option>";
    };
    //--------------------------
    print '
        </select>
    ' ;
};
#--------------------
make_datalist();
// make_drop();
#--------------------
$coke = assert($_GET['code']) ? $_GET['code'] : '' ;
$coke = strtolower($coke); 
//--------------------
//--------------------
$dsd = $code_to_lang[$coke]; 
if ($dsd == '' and $coke != '') { 
    echo "<span style='font-size:13pt;color:red'> code ($coke) not valid wiki.</span>";
} else {
    if ($coke != '') {
        $_SESSION['code'] = $coke;
    };
};
// -------------
print "</td></tr>
" ;
print '<tr><td colspan="3" class="aligncenter"> ';
//----------------------------
function printlast() {
    print '</td></tr>
    ' ;
    print "</tbody></table>
    <hr/>
    " ;
    print "</form>" ;
};
//----------------------------
?>