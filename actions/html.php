<?php
//---
function add_quotes($str) {
    $quote = preg_match("/[']+/u", $str) ? '"' : "'";
    return $quote . $str . $quote;
};
//---
function login_card() {
    return <<<HTML
    <div class='card' style='font-weight: bold;'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-md-10'>
                    <a role='button' class='btn btn-primary' onclick='login()'>
                        <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    HTML;
}

function make_modal_fade($label, $text, $id, $button='') {
    $exampleModalLabel = rand(1000, 9999);
    return <<<HTML
        
        <!-- Logout Modal-->
        <div class="modal fade" id="$id" tabindex="-1" role="dialog" aria-labelledby="$exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="$exampleModalLabel">$label</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">$text</div>
                    <div class="modal-footer">
                        $button
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    HTML;
}

function make_form_check_input($label, $name, $value_yes, $value_no, $checked) {
    //---
    $label_line = ($label != '') ? "<label class='form-check-label' for='$name'>$label</label>" : "";
    //---
    return <<<HTML
        <div class='form-check form-switch'>
            $label_line
            <input class='form-check-input' type='checkbox' name='$name' value='$value_yes' $checked>
        </div>
    HTML;
    
}
//---
function make_mail_icon($tab) {
	//---
    $mail_params = array(
		'user'   => $tab['user'], 
		'lang'   => $tab['lang'],
		'target' => $tab['target'],
		'date'   => $tab['pupdate'],
		'title'  => $tab['title'],
		'nonav'  => '1'
	);
    //---
    $mail_url = "coordinator.php?ty=msg&" . http_build_query( $mail_params );
    //---
	$onclick = 'pupwindow("' . $mail_url . '")';
    //---
    return <<<HTML
    	<a class='btn btn-outline-primary btn-sm' onclick='$onclick'>Email</a>
    HTML;
}
//---
function make_project_to_user($projects, $project){
	//---
    $str = "<option value='Uncategorized'>Uncategorized</option>";
    // $str = "";
    //---
    foreach ( $projects AS $p_title => $p_id ) {
		$cdcdc = $project == $p_title ? "selected" : "";
        $str .= <<<HTML
			<option value='$p_title' $cdcdc>$p_title</option>
		HTML;
    };
    //---
	return $str;
};
//---
function make_input_group( $label, $id, $value, $required='') {
    $val2 = add_quotes($value);
    return <<<HTML
    <div class='col-md-3'>
        <div class='input-group mb-3'>
            <span class='input-group-text'>$label</span>
            <input class='form-control' type='text' name='$id' value=$val2 $required/>
        </div>
    </div>
    HTML;
};
//---
function makeDropdown($tab, $cat, $id, $add) {
    //---
    $options = "";
    //---
    foreach ( $tab AS $dd ) {
        //---
        $se = '';
        //---
        if ( $cat == $dd ) $se = 'selected';
        //---
        $options .= <<<HTML
            <option value='$dd' $se>$dd</option>
        HTML;
        //---
    };
    //---
	$sel_line = "";
	//---
    if ($add != '' ) {
        $add2 = ($add == 'all') ? 'All' : $add;
	    $sel = "";
	    if ( $cat == $add ) $sel = "celected";
        $sel_line = "<option value='$add' $sel>$add2</option>";
    }
	//---
    return <<<HTML
        <select dir="ltr" id="$id" name="$id" class="form-select" data-bs-theme="auto">
            $sel_line
            $options
        </select>
    HTML;
};
//---
function makeCard($title, $table) {
    return <<<HTML
    <div class="card">
        <div class="card-header aligncenter" style="font-weight:bold;">
            $title
        </div>
        <div class="card-body1 card2">
            $table
        </div>
        <!-- <div class="card-footer"></div> -->
    </div>
    HTML;
};
//---
function makeColSm4($title, $table, $numb=4, $table2='', $title2='') {
    return <<<HTML
    <div class="col-md-$numb">
        <div class="card mb-3">
            <div class="card-header">
                <span class="card-title" style="font-weight:bold;">
                    $title
                </span>
                <div style='float: right'>
                    $title2
                </div>
            </div>
            <div class="card-body1 card2">
                $table
            </div>
            <!-- <div class="card-footer"></div> -->
        </div>
        $table2
    </div>
    HTML;
};
//---
function make_col_sm_body($title, $subtitle, $table, $numb=4) {
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
        $options .= <<<HTML
		<option value='$cod' $cdcdc>$name</option>
		
		HTML;
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
        return "<a target='_blank' href='https://mdwiki.org/wiki/$encoded_title'>$title</a>";
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
function make_translation_url($title, $lang, $tr_type) {
    //---
    $page = $tr_type == 'all' ? "User:Mr. Ibrahem/$title/full" : "User:Mr. Ibrahem/$title";
    //---
    $params = array(
        'page' => $page,
        'from' => "en",
        'sx' => 'true',
        'to' => $lang,
        'targettitle' => $title
    );
    //---
    $url = "//$lang.wikipedia.org/wiki/Special:ContentTranslation";
    //---
    // $url .= "?" . http_build_query($params) . "#/sx/sentence-selector";
    $url .= "?" . http_build_query($params) . "#/sx?previousRoute=dashboard&eventSource=direct_preselect";
    //---
    // $url = "//$lang.wikipedia.org/wiki/Special:ContentTranslation?page=User%3AMr.+Ibrahem%2F$title&from=en&to=$lang&targettitle=$title#draft";
    //---
    return $url;
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