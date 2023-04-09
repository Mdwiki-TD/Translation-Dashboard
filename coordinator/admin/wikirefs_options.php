<?php
//---
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
?>
<div class='card-header'>
    <h4>Fix wikirefs options:</h4>
</div>
<div class='card-body'>
<?PHP
//---
include_once('td_config.php');
//---
$tabes = get_configs('fixwikirefs.json');
//---
if (isset($_POST['newlang'])) {
    if (count($_POST['newlang']) != null) {
        for($i = 0; $i < count($_POST['newlang']); $i++ ){
            //---
            $lang1  	 = $_POST['newlang'][$i] ?? '';
            $move_dots1  = ($_POST['newmove_dots'][$i] ?? '') == '1' ? 1 : 0;
            $expend1     = ($_POST['newexpend'][$i] ?? '') == '1' ? 1 :0;
            //---
            $lang1 = strtolower($lang1);
            //---
            $tabes[$lang1] = array();
            $tabes[$lang1]['move_dots'] = $move_dots1;
            $tabes[$lang1]['expend'] = $expend1;
            //---
        };
        //---
    };
};
//---
if (isset($_POST['lang'])) {
    if (count($_POST['lang']) != null) {
        for($io = 0; $io < count($_POST['lang']); $io++ ){
            //---
            $lang = strtolower($_POST['lang'][$io]);
            //---
            $tabes[$lang] = array();
            $tabes[$lang]['move_dots'] = 0;
            $tabes[$lang]['expend'] = 0;
            //---
        };
        //---
    };
};
//---
function add_key_from_post($key) {
    global $tabes;
    //---
    if (isset($_POST[$key])) {
        if (count($_POST[$key]) != null) {
            for($io = 0; $io < count($_POST[$key]); $io++ ){
                //---
                $vav = strtolower($_POST[$key][$io]);
                //---
                if (!isset($tabes[$vav])) $tabes[$vav] = array();
                $tabes[$vav][$key] = 1;
                //---
            };
        };
    };
};
//---
$keys_to_add = array('move_dots', 'expend', 'add_en_lang');
//---
foreach ($keys_to_add as $key) {
    add_key_from_post($key);
};
//---
if (isset($_POST['del'])) {
    for($i = 0; $i < count($_POST['del']); $i++ ) {
        $key_to_del	= $_POST['del'][$i];
        //---
        if (isset($tabes[$key_to_del])) unset($tabes[$key_to_del]);
    };
};
//---
if (isset($_POST['lang']) || isset($_POST['newlang'])) {
    //---
    $tabes2 = $tabes;
    //---
    foreach ( $tabes AS $lang => $tab ) {
        foreach ($keys_to_add as $key) {
            if (!isset($tabes2[$lang][$key])) $tabes2[$lang][$key] = 0;
        };
    };
    //---
    set_configs_all_file('fixwikirefs.json', $tabes2);
};
//---
$sato = <<<HTML
<form action="coordinator.php?ty=wikirefs_options" method="POST">
    <input name="ty" value="wikirefs_options" hidden/>
    <table id="em2" class="table table-sm table-striped" style="font-size:90%;">
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
            <td>$numb</td>
            <td>
                <span>$lang</span>
                <input name='lang[]$numb' value='$lang' hidden/>
            </td>
            <td>
                <div class='form-check form-switch'>
                    <input class='form-check-input' type='checkbox' name='move_dots[]$numb' value='$lang' $move_dots/>
                </div>
            </td>
            <td>
                <div class='form-check form-switch'>
                    <input class='form-check-input' type='checkbox' name='expend[]$numb' value='$lang' $expend2/>
                </div>
            </td>
            <td>
                <div class='form-check form-switch'>
                    <input class='form-check-input' type='checkbox' name='add_en_lang[]$numb' value='$lang' $add_en_lang/>
                </div>
            </td>
            <td>
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
	e = e + "<td><input name='newlang[]" + ii + "' id='newlang[]" + ii + "'/></td>";
	e = e + "<td><input type='checkbox' name='newmove_dots[]" + ii + "'  id='newmove_dots[]" + ii + "' value='1'/></td>";
	e = e + "<td><input type='checkbox' name='newexpend[]" + ii + "' id='newexpend[]" + ii + "' value='1'/></td>";
	e = e + "</tr>";

	$('#tab_ma').append(e);
	i++;
};

$(document).ready( function () {
	$('#em2').DataTable({
	    lengthMenu: [[50, 100], [50, 100]],
        // paging: false,
        // searching: false
	});
} );

</script>