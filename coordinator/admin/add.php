
<style>
.ui-menuxx {
	height: 200px;
}
</style>
<?php
//---
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
require('tables.php');
//---
$hoste = 'https://tools-static.wmflabs.org/cdnjs';
if ( $_SERVER['SERVER_NAME'] == 'localhost' )  $hoste = 'https://cdnjs.cloudflare.com';
echo <<<HTML
	<script src='$hoste/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js'></script>
	<link rel='stylesheet' href='$hoste/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css'/>
HTML;
//---
?>
<div class='card-header'>
	<h4>Add translations:</h4>
</div>
<div class='cardbody'>
<form action="coordinator.php?ty=add" method="POST">
	<input name='ty' value="add" hidden/>
	  	<div class="form-group">
			<table class='table' style='font-size:95%;'>
				<tr>
					<th>#</th>
					<th>mdwiki title</th>
					<th>Campaign</th>
					<th>Type</th>
					<th>User</th>
					<th>Lang.</th>
					<th>Target</th>
					<th>Pupdate</th>
				</tr>
				<tbody id='g_tab'>
<?php
//---
function qu_str($string) {
	$str2 = "'$string'";
	//---
	if (strpos($string, "'") !== false)	$str2 = '"' . $string . "'";
	//---
	return $str2;
};
//---
function add_to_db($title, $type, $cat, $lang, $user, $target, $pupdate) {
    //---
	global $Words_table, $All_Words_table;
    //---
    $user 		= rawurldecode($user);
    $cat		= rawurldecode($cat);
    $title2		= qu_str($title);
    $target2	= qu_str($target);
    //---
    $word = $Words_table[$title] ?? 0; 
    if ($type == 'all') $word = $All_Words_table[$title] ?? 0;
    //---
	// date now format like 2023-01-01
	$add_date = date('Y-m-d');
	//---
	$qua_23 = "
	UPDATE pages 
		SET target = $target2, pupdate = '$pupdate', word = '$word'
	WHERE user = '$user' AND title = $title2 AND lang = '$lang' and target = ''
	;

	INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
		SELECT '$title', '$word', '$type', '$cat', '$lang', now(), '$user', '$pupdate', $target2, '$add_date'
	WHERE NOT EXISTS (SELECT 1 FROM pages WHERE title = $title2 AND lang = '$lang' AND user = '$user' );

	";
    //---
	if (isset($_REQUEST['test'])) echo $qua_23;
    //---
    execute_query($qua_23);
    //---
};
//---
if (isset($_POST['mdtitle'])) {
	for($i = 0; $i < count($_POST['mdtitle']); $i++ ) {
		//---
		$mdtitle	= $_REQUEST['mdtitle'][$i] ?? '';
		$cat		= $_REQUEST['cat'][$i] ?? '';
		$type		= $_REQUEST['type'][$i] ?? '';
		$user		= $_REQUEST['user'][$i] ?? '';
		$lang		= $_REQUEST['lang'][$i] ?? '';
		$target		= $_REQUEST['target'][$i] ?? '';
		$pupdate	= $_REQUEST['pupdate'][$i] ?? '';
		//---
		if ($mdtitle != '' && $lang != '' && $user != '' && $target != '') {
			//---
			add_to_db($mdtitle, $type, $cat, $lang, $user, $target, $pupdate);
			//---
		};
	};
};
//---
$cats = "";
//---
$qqq = execute_query('select category, display from categories;');
//---
foreach ($qqq AS $Key => $ta ) {
	$ca = $ta['category'];
	$ds = $ta['display'];
	if ($ca != '') $cats .= "<option value='$ca'>$ds</option>";
};
//---
$typies = <<<HTML
	<select name='type[]%s' id='type[]%s' class='form-select'>
		<option value='lead'>Lead</option><option value='all'>All</option>
	</select>
	HTML;
//---
$table = "";
//---
foreach ( range(1, 1) as $numb ) {
    //---
	$cats_line = "<select class='form-select catsoptions' name='cat[]$numb' id='cat[]$numb'>$cats</select>";
	$type_line = sprintf($typies, $numb, $numb);
    //---
	$table .= <<<HTML
	<tr>
	  <td data-order='$numb'>$numb</td>
	  <td> <input size='15' class='mdtitles' name='mdtitle[]$numb' id='mdtitle[]$numb' required/> </td>
	  <td> $cats_line </td>
	  <td> $type_line </td>
	  <td> <input size='10' class='useri' name='user[]$numb' id='user[]$numb' required/> </td>
	  <td> <input size='2' name='lang[]$numb' id='lang[]$numb' required/> </td>
	  <td> <input size='20' name='target[]$numb' id='target[]$numb' required/>	</td>
	  <td> <input size='10' name='pupdate[]$numb' id='pupdate[]$numb' required/> </td>
	</tr>
	HTML;
};
//---
$testin = (($_REQUEST['test'] ?? '') != '') ? "<input name='test' value='1' hidden/>" : "";
//---
$table .= <<<HTML
	</tbody>
	</table>
	$testin
HTML;
//---
echo $table;
?>

  <button type="submit" class="btn btn-success mb-10">send</button>
</form>
<span role='button' id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
</div>

<script type="text/javascript">
var i = 1;
function add_row() {
	var options = $('.catsoptions').html();
	var ii = $('#g_tab >tr').length + 1;
	var e = "<tr>";
	e = e + "<td>" + ii + "</td>";
	e = e + "<td>	<input size='15' class='mdtitles' name='mdtitle[]" + ii + "' id='mdtitle[]" + ii + "' required/>	</td>";
	e = e + "<td><select class='form-select catsoptions' name='cat[]" + ii + "' id='cat[]" + ii + "'>" + options + "</select></td>";
	e = e + "<td><select name='type[]%s' id='type[]%s' class='form-select'>";
	e = e + "<option value='lead'>Lead</option><option value='all'>All</option></select></td>";
	e = e + "<td>	<input size='10' class='useri' name='user[]" + ii + "' id='user[]" + ii + "' required/>	</td>";
	e = e + "<td>	<input size='2' name='lang[]" + ii + "' id='lang[]" + ii + "' required/>	</td>";
	e = e + "<td>	<input size='20' name='target[]" + ii + "' id='target[]" + ii + "' required/>	</td>";
	e = e + "<td>	<input size='10' name='pupdate[]" + ii + "' id='pupdate[]" + ii + "' required/>	</td>";
	e = e + "<td></td>";
	e = e + "</tr>";
	$('#g_tab').append(e);
	i++;
};
</script>
<?PHP

$script = <<<HTML
<script>
$( function() {
    var availableTags = [
      %s
    ];
    $( ".useri" ).autocomplete({
      source: availableTags
    });
    
});
</script>
HTML;
//---
$ka = '';
//---
foreach(execute_query('SELECT DISTINCT user from pages;') as $k => $tab) {
	$u = $tab['user'];
	$ka .= '"' . $u . '",
	';
};
//---
echo sprintf($script, $ka);
//---
?>
<!-- 
<script>
$( function() {
	var ur = 'cats_cash/RTT.json';
    $( ".mdtitles" ).autocomplete({
	source: function (request, response){
		$.ajax({url: ur ,dataType: "json",data:{term: request.term,},success: function (data) {response(data.list);}});
	}
    });
});
</script> 
-->
</div>