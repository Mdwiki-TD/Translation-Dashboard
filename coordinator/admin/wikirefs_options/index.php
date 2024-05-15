<?php
//---
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
include_once 'td_config.php';
//---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'post.php';
}
//---
$tabes = get_configs('fixwikirefs.json');
//---
echo <<<HTML
	<div class='card-header'>
		<h4>Fix wikirefs options:</h4>
	</div>
	<div class='card-body'>
HTML;
//---
$testin = (($_REQUEST['test'] ?? '') != '') ? "<input name='test' value='1' hidden/>" : "";
//---
$sato = <<<HTML
<form action="coordinator.php?ty=wikirefs_options" method="POST">
	$testin
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
        <tbody id="refs_tab">
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
    $add_en_lng     = ($tabg['add_en_lng'] == 1) ? 'checked' : '';
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
                    <input class='form-check-input' type='checkbox' name='add_en_lng[]$numb' value='$lang' $add_en_lng/>
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
        $tabes[$lal] = array('expend' => 0, 'move_dots' => 0, 'add_en_lng' => 0);
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
		<button type="submit" class="btn btn-outline-primary">Save</button>
		<span role="button" id="add_row" class="btn btn-outline-primary" style="position: absolute; right: 130px;" onclick="add_row()">New row</span>
	</form>

HTML;
print $sato;
//---
?>

<script type="text/javascript">
    var ii = $('#refs_tab >tr').length;
    function add_row() {
        ii++;
        var e = "<tr>";
        e = e + "<td>" + ii + "</td>";
        e = e + "<td><input class='form-control' name='newlang[]" + ii + "' placeholder='lang code.'/></td>";
        e = e + "<td><input class='form-control' type='text' name='move_dotsx[]" + ii + "' value='0' disabled/></td>";
        e = e + "<td><input class='form-control' type='text' name='expendx[]" + ii + "' value='0' disabled/></td>";
        e = e + "<td><input class='form-control' type='text' name='add_en_lngx[]" + ii + "' value='0' disabled/></td>";
        e = e + "<td>-</td>";
        e = e + "</tr>";

        $('#refs_tab').append(e);
    };

    $(document).ready( function () {
        $('#em2').DataTable({
            lengthMenu: [[10, 50, 100, 150], [10, 50, 100, 150]],
            // paging: false,
            // searching: false
        });
    } );

</script>
