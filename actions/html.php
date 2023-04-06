<?php
//---
function add_quotes($str) {
    $quote = preg_match("/[']+/u", $str) ? '"' : "'";
    return $quote . $str . $quote;
};
//---
function make_input_group( $label, $id, $value, $required='') {
    $val2 = add_quotes($value);
    return <<<HTML
    <div class='col-md-3'>
        <div class='input-group mb-3'>
            <div class='input-group-prepend'>
                <span class='input-group-text'>$label</span>
            </div>
            <input class='form-control' type='text' name='$id' value=$val2 $required/>
        </div>
    </div>
    HTML;
};
//---
function make_drop_d($tab, $cat, $id, $add) {
    //---
    $options = "";
    //---
    foreach ( $tab AS $dd ) {
        //---
        $se = '';
        //---
        if ( $cat == $dd ) $se = 'selected';
        //---
        $options .= "
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
    return <<<HTML
        <select dir="ltr" id="$id" name="$id" class="form-select">
            $sel_line
            $options
        </select>
    HTML;
};
//---
function make_col_sm_4($title, $table, $numb = '4') {
    return <<<HTML
    <div class="col-md-$numb">
        <div class="card">
            <div class="card-header aligncenter" style="font-weight:bold;">
                $title
            </div>
            <div class="card-body1 card2">
                $table
            </div>
            <!-- <div class="card-footer"></div> -->
        </div>
        <br>
    </div>
    HTML;
};
//---
function make_col_sm_body($title, $subtitle, $table, $numb = '4') {
    return <<<HTML
        <div class="col-md-$numb">
            <div class="card">
                <div class="card-header aligncenter1">
                    <span style="font-weight:bold;">$title</span> $subtitle
                </div>
                <div class="card-body card2">
                    $table
                </div>
            </div>
            <br>
        </div>
    HTML;
};
//---
function make_drop($uxutable, $code) {
    $options  =  "";
    //---
    foreach ($uxutable AS $name => $cod) {
        $cdcdc = $code == $cod ? "selected" : "";
        $options .= "
		<option value='$cod' $cdcdc>$name</option>
		";
    };
    //---
	return $options;
};
//---
function make_datalist_options($hyh) {
    $options = '';
    foreach ($hyh as $language => $code) {
        $options .= "<option value='$code'>$language</option>";
    }
    return $options;
}
//---
function make_mdwiki_title($title) {
    if ($title != '') {
        $encoded_title = rawurlencode(str_replace(' ', '_', $title));
        return "<a href='https://mdwiki.org/wiki/$encoded_title'>$title</a>";
    }
    return $title;
}
//---
function make_cat_url($category) {
    if ($category != '') {
        $encoded_category = rawurlencode(str_replace(' ', '_', $category));
        return "<a href='https://mdwiki.org/wiki/Category:$encoded_category'>$category</a>";
    }
    return $category;
}
//---
function make_mdwiki_user_url($user) {
    if ($user != '') {
        $encoded_user = rawurlencode(str_replace(' ', '_', $user));
        return "<a href='https://mdwiki.org/wiki/User:$encoded_user'>$user</a>";
    }
    return $user;
}
//---
function make_target_url($target, $lang, $name = '') {
    $display_name = ($name != '') ? $name : $target;
    if ($target != '') {
        $encoded_target = rawurlencode(str_replace(' ', '_', $target));
        return "<a href='https://$lang.wikipedia.org/wiki/$encoded_target'>$display_name</a>";
    }
    return $target;
}
//---
?>