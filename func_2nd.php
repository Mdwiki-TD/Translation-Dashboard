<?PHP
//===
function make_col_sm_4($title, $table, $numb = '4') {
    return "
    <div class='col-sm-$numb'>
      <div class='panel panel-default'>
          <div class='panel-heading aligncenter' style='font-size:110%;font-weight:bold;'>
              $title
          </div>
          <div class='panel-body' style='padding:5px 0px 5px 5px; max-height:800px; overflow: auto;'>
            $table
          </div>
      </div>
    </div>
    ";
};
//=== 
function make_col_sm_4_new($title, $table, $numb = '4') {
    return "
    <div class='col-sm-$numb'>
      <div class='panel panel-default'>
          <div class='panel-heading aligncenter' style='font-size:110%;font-weight:bold;'>
              $title
          </div>
          <!-- <div class='panel-body' style='padding:5px 0px 5px 5px; max-height:330px; overflow: auto;'> -->
            $table
          <!-- </div> -->
      </div>
    </div>
    ";
};
//=== 
function test_print($s) {
    global $test;
    if ($test != '') { print $s; };
};
//===
function make_drop($uxutable, $code, $id) {
    $ux =  "<select dir='ltr' id='$id' name='$id' class='form-control custom-select'>";
    //---
    foreach ( $uxutable AS $name => $cod ) {
        $cdcdc = $code == $cod ? "selected" : "";
        $ux .= "
		<option value='$cod' $cdcdc>$name</option>
		";
    };
    //---
    $ux .= '</select>' ;
    //---
	return $ux;
};
//===
function make_datalist_options($hyh) {
    //---
    $str = '';
    //---
    foreach ( $hyh AS $lange => $cod ) {
        $str .= "
            <option value='$cod'>$lange</option>";
    };
    //---
    return $str;
    //---
};
//===
function Get_it( $array, $key ) {
    $uu = isset($array[$key]) ? $array[$key] : $array->{$key};
    return $uu;
};
//===
function make_view_by_number($target , $numb, $lang) {
    //---
    $numb2 = ($numb != '') ? $numb : "?";
    //---
    $urln = 'https://' . 'pageviews.toolforge.org/?project='. $lang .'.wikipedia.org&platform=all-access&agent=all-agents&redirects=0&range=this-year&pages=' . rawurlEncode($target);
    //---
    $link = '<a target="_blank" href="' . $urln . '">' . $numb2 . '</a>';
    //---
    return $link ;
    };
//===
function make_mdwiki_title($tit) {
    $title = $tit;
    if ($title != '') {
        $title2 = rawurlencode( str_replace ( ' ' , '_' , $title ) );
        $title = '<a href="https://mdwiki.org/wiki/' . $title2 . '">' . $title . '</a>';
    };
    return $title;
};
//=== 
function make_cat_url ($ca) {
    $cat = $ca;
    if ($cat != '') {
        $cat2 = rawurlencode( str_replace ( ' ' , '_' , $cat ) );
        $cat = '<a href="https://mdwiki.org/wiki/Category:' . $cat2 . '">' . $cat . '</a>';
    };
    return $cat;
};
//=== 
function make_mdwiki_user_url($ud) {
    $user = $ud;
    if ($user != '') {
        $user2 = rawurlencode( str_replace ( ' ' , '_' , $user ) );
        $user = '<a href="https://mdwiki.org/wiki/User:' . $user2 . '">' . $user . '</a>';
    };
    return $user;
};
//=== 
function make_target_url ($ta , $lang) {
    $target = $ta ;
    if ($target != '') {
        $target2 = rawurlencode( str_replace ( ' ' , '_' , $target ) );
        $target = '<a href="https://' . $lang . '.wikipedia.org/wiki/' . $target2 . '">' . $target . '</a>';
    };
    return $target;
};
//=== 
if ($_REQUEST['test'] != '' ) echo "<br>load " . str_replace ( __dir__ , '' , __file__ ) . " true.";
//=== 
?>