<?php
//---
function add_quotes($str) {
	// if str have ' then use "
	// else use '
	$value = "'$str'";
	if (preg_match("/[']+/", $str)) $value = '"$str"';
	return $value;
};
//---
function make_input_group( $label, $id, $value, $required) {
    $val2 = add_quotes($value);
    $str = "
    <div class='col-md-3'>
        <div class='input-group mb-3'>
            <div class='input-group-prepend'>
                <span class='input-group-text'>$label</span>
            </div>
            <input class='form-control' type='text' name='$id' value=$val2 $required/>
        </div>
    </div>";
    return $str;
};
//---
function make_drop_d($tab, $cat, $id, $add) {
    //---
    $lines = "";
    //---
    foreach ( $tab AS $dd ) {
        //---
        $se = '';
        //---
        if ( $cat == $dd ) $se = 'selected';
        //---
        $lines .= "
	    <option value='$dd' $se>$dd</option>
		";
        //---
    };
    //---
	$sel_line = "";
	//---
    if ($add != '' ) {
	    $sel = "";
	    if ( $cat == $add ) $sel = "celected";
        $sel_line = "<option value='$add' $sel>$add</option>";
    }
	//---
    $texte = "
        <select dir='ltr' id='$id' name='$id' class='form-select'>
            $sel_line
			$lines
        </select>";
    //---
    return $texte;
    //---
};
//---
function make_col_sm_4($title, $table, $numb = '4') {
    return "
    <div class='col-md-$numb'>
      <div class='card'>
          <div class='card-header aligncenter' style='font-weight:bold;'>
              $title
          </div>
          <div class='card-body1 card2'>
            $table
          </div>
          <!-- <div class='card-footer'></div> -->
      </div>
      <br>
    </div>
    ";
};
//---
function make_col_sm_body($title, $subtitle, $table, $numb = '4') {
    return "
    <div class='col-md-$numb'>
        <div class='card'>
            <div class='card-header aligncenter1'>
                <span style='font-weight:bold;'>$title</span> $subtitle
            </div>
            <div class='card-body card2'>
                $table
            </div>
        </div>
        <br>
    </div>
    ";
};
//---
function make_drop($uxutable, $code) {
    $ux =  "";
    //---
    foreach ( $uxutable AS $name => $cod ) {
        $cdcdc = $code == $cod ? "selected" : "";
        $ux .= "
		<option value='$cod' $cdcdc>$name</option>
		";
    };
    //---
	return $ux;
};
//---
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
//---
function make_mdwiki_title($tit) {
    $title = $tit;
    if ($title != '') {
        $title2 = rawurlencode( str_replace ( ' ' , '_' , $title ) );
        $title = '<a href="https://mdwiki.org/wiki/' . $title2 . '">' . $title . '</a>';
    };
    return $title;
};
//--- 
function make_cat_url ($ca) {
    $cat = $ca;
    if ($cat != '') {
        $cat2 = rawurlencode( str_replace ( ' ' , '_' , $cat ) );
        $cat = '<a href="https://mdwiki.org/wiki/Category:' . $cat2 . '">' . $cat . '</a>';
    };
    return $cat;
};
//--- 
function make_mdwiki_user_url($ud) {
    $user = $ud;
    if ($user != '') {
        $user2 = rawurlencode( str_replace ( ' ' , '_' , $user ) );
        $user = '<a href="https://mdwiki.org/wiki/User:' . $user2 . '">' . $user . '</a>';
    };
    return $user;
};
//---
function make_target_url($ta, $lang, $name='') {
    $target = $ta ;
	//---
	$nan = $target;
	if ($name != '') $nan = $name;
	//---
    if ($target != '') {
        $target2 = rawurlencode( str_replace ( ' ' , '_' , $target ) );
        $target = '<a href="https://' . $lang . '.wikipedia.org/wiki/' . $target2 . '">' . $nan . '</a>';
    };
    return $target;
};
//--- 
?>