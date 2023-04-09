<?php
//---
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
?>
<div class='card-header'>
	<h4>Settings:</h4>
</div>
<div class='card-body'>  
    <div class='row'>
<?PHP
//---
include_once('functions.php');
include_once('td_config.php');
//---
$conf = get_configs('conf.json');
//---
$display_type = $_REQUEST['display_type'];
if (isset($display_type)) {
    $value = ($display_type == '0') ? false : true;
    set_configs('conf.json', 'allow_type_of_translate', $value);
};
//---
$conf = get_configs('conf.json');
//---
$allow_whole_translate = $conf['allow_type_of_translate'] ?? true;
//---
$checked_yes = '';
$checked_no = '';
//---
if ($allow_whole_translate == true) { 
    $checked_yes = 'checked';
} else {
    $checked_no = 'checked';
};
//---
$uux = "
<form action='coordinator.php' method='POST'>
    <input name='ty' value='settings' hidden/>
    <div class='input-group mb-3'>
        <div class='col-md-3'>
            <div class='input-group-prepend'>
                <span class='input-group-text'><b>Translate type: </b> (Default: lead)</span>
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-control'>
                <div class='form-check form-check-inline'>
                    <input type='radio' class='form-check-input' id='trRadio' name='display_type' value='1' $checked_yes>
                    <label class='form-check-label' for='trRadio'>Display</label>
                </div>
                <div class='form-check form-check-inline'>
                    <input type='radio' class='form-check-input' id='trRadio2' name='display_type' value='0' $checked_no>
                    <label class='form-check-label' for='trRadio2'>Hide</label>
                </div>
            </div>
        </div>
        <div class='col-md-4'>
            <div class='aligncenter'>
                <button type='submit' class='btn btn-success'>send</button>
            </div>
        </div>
    </div>
</form>
";
//---
$tat = "
    <form action='coordinator.php' method='POST'>
        <input name='ty' value='settings' hidden/>
        <div class='form-check form-check-inline'>
            <input type='radio' class='form-check-input' id='customRadio' name='display_type' value='1' $checked_yes>
            <label class='form-check-label' for='customRadio'>Display</label>
        </div>
        <div class='form-check form-check-inline'>
            <input type='radio' class='form-check-input' id='customRadio2' name='display_type' value='0' $checked_no>
            <label class='form-check-label' for='customRadio2'>Hide</label>
        </div>
        <button type='submit' class='btn btn-success'>send</button>
    </form>
";
// echo $uux;
$div3 = make_col_sm_body('Translate type:', '(Default: "lead")',$tat, $numb = '4');
echo $div3;
/*
$nn = 0;
foreach(execute_query('SELECT count(DISTINCT user) as c from pages;') as $k => $tab) $nn = $tab['c'];
//---
echo "<h4>Users: ($nn user):</h4>";
//---
*/
?>
    </div>
</div>