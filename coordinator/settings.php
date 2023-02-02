
<div class='card-header'>
	<h4>Settings:</h4>
</div>
<div class='card-body'>  
    <div class='row'>

<?PHP
//---
include_once('functions.php');
//---
$conf = get_configs();
//---
$display_type = $_REQUEST['display_type'];
if (isset($display_type)) {
    set_configs('allow_type_of_translate', $display_type);
};
//---
$conf = get_configs();
//---
$allow_whole_translate = isset($conf['allow_type_of_translate']) ? $conf['allow_type_of_translate'] : true;
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
        <div class='form-group'>
            <div class='aligncenter'>
                <button type='submit' class='btn btn-success'>send</button>
            </div>
        </form>
    </div>
";
$div3 = make_col_sm_body('Translate type:', '(Default: "lead")',$tat, $numb = '4');
echo $div3;
/*
$nn = 0;
foreach(quary2('SELECT count(DISTINCT user) as c from pages;') as $k => $tab) $nn = $tab['c'];
//---
echo "<h4>Users: ($nn user):</h4>";
//---
*/
?>
    </div>
</div>