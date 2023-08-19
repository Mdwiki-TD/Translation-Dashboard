<?php
//---
?>
<div class='card-header'>
    <h4>Fix wikirefs options:</h4>
</div>
<div class='card-body'>
<?PHP
//---
$sato = <<<HTML
<form action="coordinator.php?ty=wikirefs_options" method="POST">
    <input name="ty" value="wikirefs_options" hidden/>
    <table id="em2" class="table table-sm table-striped table-mobile-responsive table-mobile-sided" style="font-size:90%;">
        <thead>
            <tr>
                <th>#</th>
                <th>Lang.</th>
                <th>Move dots</th>
                <th>Expend infobox</th>
                <th>add |language=en</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody id="tab_ma">
HTML;
//---
/*
$ul = '<lu>';
foreach ($_POST as $key => $values) {
    $vv = var_export($values, $return = true);
    $ul .= "<li>$key:$vv</li>";
};
$ul .= "</lu>";
echo $ul;*/ 
//---
function make_td($lang, $tabg, $numb) {
    //---
    $lang = strtolower($lang);
    //---
    $expend2        = ($tabg['expend'] == 1) ? 'checked' : '';
    $move_dots      = ($tabg['move_dots'] == 1) ? 'checked' : '';
    $add_en_lang    = ($tabg['add_en_lang'] == 1) ? 'checked' : '';
    //---
    $laly = <<<HTML
        <tr>
            <td data-content='#'>
                $numb
            </td>
            <td data-content='#'>
                <span>$lang</span>
                <input name='lang[]$numb' value='$lang' hidden/>
            </td>
            <td data-content='Move dots'>
                <div class='form-check form-switch'>
                    <input class='form-check-input' type='checkbox' name='move_dots[]$numb' value='$lang' $move_dots/>
                </div>
            </td>
            <td data-content='Expend infobox'>
                <div class='form-check form-switch'>
                    <input class='form-check-input' type='checkbox' name='expend[]$numb' value='$lang' $expend2/>
                </div>
            </td>
            <td data-content='Add |language=en'>
                <div class='form-check form-switch'>
                    <input class='form-check-input' type='checkbox' name='add_en_lang[]$numb' value='$lang' $add_en_lang/>
                </div>
            </td>
            <td data-content='Delete'>
                <input type='checkbox' name='del[]$numb' value='$lang'>
            </td>
        </tr>
        HTML;
    //---
    return $laly;
};
//---
foreach ( execute_query("select DISTINCT lang from pages;") AS $tat => $tag ) {
    $lal = strtolower($tag['lang']);
    //---
    if (!isset($tabes[$lal])) {
        $tabes[$lal] = array('expend' => 0, 'move_dots' => 0, 'add_en_lang' => 0);
    };
};
//---
ksort($tabes);
//---
$n = -1;
foreach ( $tabes AS $lang => $tab ) {
    //---
    $n += 1;
    $sato .= make_td($lang, $tab, $n);
    //---
};
//---
$sato .= <<<HTML
			</tbody>
		</table>
		<button type="submit" class="btn btn-success">Submit</button>
		<span role="button" id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick="add_row()">New row</span>
	</form>

HTML;
print $sato;
//---
?>

<script type="text/javascript">
var ii = 1;
function add_row() {
	var e = "<tr>";
	e = e + "<td>" + ii + "</td>";
	e = e + "<td><input name='newlang[]" + ii + "'/></td>";
	e = e + "<td><input type='checkbox' name='newmove_dots[]" + ii + "'  id='newmove_dots[]" + ii + "' value='1'/></td>";
	e = e + "<td><input type='checkbox' name='newexpend[]" + ii + "' value='1'/></td>";
	e = e + "</tr>";

	$('#tab_ma').append(e);
	i++;
};

$(document).ready( function () {
	$('#em2').DataTable({
	    lengthMenu: [[50, 100, 150], [50, 100, 150]],
        // paging: false,
        // searching: false
	});
} );

</script>