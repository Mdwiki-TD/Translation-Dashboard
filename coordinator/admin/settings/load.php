<div class='card-header'>
	<h4>Settings:</h4>
</div>
<div class='card-body'>  
    <div class='row'>
<?PHP
//---
include_once('functions.php');
//---
$allow_whole_translate = $conf['allow_type_of_translate'] ?? true;
//---
$checked_yes = ($allow_whole_translate == true) ? 'checked' : '';
$checked_no  = ($allow_whole_translate != true) ? 'checked' : '';
//---
$tat = <<<HTML
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

    HTML;
//---
$div3 = make_col_sm_body('Translate type:', '(Default: "lead")',$tat, 4);
echo $div3;
//---
?>
    </div>
</div>