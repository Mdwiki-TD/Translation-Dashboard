<div class='card-header'>
	<h4>Settings:</h4>
</div>
<div class='card-body'>  
    <div class='row'>
<?PHP
//---
include_once('functions.php');
//---
echo <<<HTML
    <form action='coordinator.php' method='POST'>
        <input name='ty' value='settings' hidden/>
    HTML;
//---
function make_settings_tab($tabe) {
    //---
    global $nn;
    //---
    $tab = <<<HTML
            <table class='table' style='font-size:95%;width:100%;'>
                <tr>
                    <th>#</th>
                    <th>Option</th>
                    <th>Value</th>
                </tr>
                <tbody>
    HTML;
    //---
    foreach ($tabe as $key => $v) {
        $nn += 1;
        $id       = $v['id'];
        $title    = $v['title'];
        $displayed= $v['displayed'];
        $value    = $v['value'];
        //---
        $type     = $v['type'];
        //---
        $value_line = <<<HTML
            <input class='form-control' size='4' name='value_$nn' value='$value'/>
        HTML;
        //---
        if ($type == 'check') {
            $checked = ($value == 1 || $value == "1") ? 'checked' : '';
            $value_line = <<<HTML
                <div class='form-check form-switch'>
                    <input type='hidden' name='value_$nn' value='0'>
                    <input class='form-check-input' type='checkbox' name='value_$nn' value='1' $checked>
                </div>
            HTML;            
        }
        //---
        $tr = <<<HTML
            <tr>
                <input name='se[]' value='$nn' hidden/>
                <td data-order='$nn'>$nn<input name='id_$nn' value='$id' hidden/></td>
                <td>
                    $displayed
                    <input class='form-control' name='title_$nn' value='$title' hidden/>
                    <input class='form-control' name='displayed_$nn' value='$displayed' hidden/>
                </td>
                <td>
                    $value_line
                    <input class='form-control' name='type_$nn' value='$type' hidden/>
                </td>
            </tr>
        HTML;
        //---
        $tab .= $tr;
        //---
    };
    //---
    $result = <<<HTML
        <div class='form-group'>
                $tab
                </tbody>
            </table>
        </div>
    HTML;
    //---
    return $result;
    //---
};
//---
$qq = execute_query('select id, title, displayed, type, value from settings;');
//---
// var_export($qq);
//---
$text = make_settings_tab($qq);
//---
echo $text;
//---
echo "
            <button type='submit' class='btn btn-success'>send</button>
        </form>
    </div>
</div>";
//---
?>