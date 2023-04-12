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
$uux = <<<HTML
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
HTML;
//---
// echo $uux;
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
$div3 = make_col_sm_body('Translate type:', '(Default: "lead")',$tat, $numb = '4');
echo $div3;
//---
?>
    </div>
</div>