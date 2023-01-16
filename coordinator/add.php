
<style>
.ui-menuxx {
	height: 200px;
}
</style>
<?php
//---
require('tables.php');
//---
$hoste = 'https://tools-static.wmflabs.org/cdnjs';
if ( $_SERVER['SERVER_NAME'] == 'localhost' )  $hoste = 'https://cdnjs.cloudflare.com';
echo "
<script src='$hoste/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js'></script>
<link rel='stylesheet' href='$hoste/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css'/>
";
//---
?>
<div class='card-header'>
	<h4>Add translations:</h4>
</div>
<div class='card-body'>
<form action="coordinator.php?ty=add" method="POST">
	<input name='ty' value="add" hidden/>
	  <div class="form-group">
		<table class='table'>
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
			<tbody id="g_tab">
<?php
//---
function get_request_1( $key, $i ) {
    $uu = isset($_POST[$key][$i]) ? $_REQUEST[$key][$i] : '';
    return $uu;
};
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
    $word = isset($Words_table[$title]) ? $Words_table[$title] : 0; 
    if ($type == 'all') $word = isset($All_Words_table[$title]) ? $All_Words_table[$title] : 0;
    //---
	$qua_23 = "
	UPDATE pages 
		SET target = $target2, pupdate = '$pupdate', word = '$word'
	WHERE user = '$user' AND title = $title2 AND lang = '$lang' and target = ''
	;

	INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target)
		SELECT '$title', '$word', '$type', '$cat', '$lang', '', '$user', '$pupdate', $target2
	WHERE NOT EXISTS (SELECT 1 FROM pages WHERE title = $title2 AND lang = '$lang' AND user = '$user' );

	";
    //---
	if ($_REQUEST['test']) echo $qua_23;
    //---
    quary2($qua_23);
    //---
};
//---
for($i = 0; $i < count($_POST['mdtitle']); $i++ ) {
	//---
	$mdtitle	= get_request_1('mdtitle', $i);
	$cat		= get_request_1('cat', $i);
	$type		= get_request_1('type', $i);
	$user		= get_request_1('user', $i);
	$lang		= get_request_1('lang', $i);
	$target		= get_request_1('target', $i);
	$pupdate	= get_request_1('pupdate', $i);
	//---
	if ($mdtitle != '' && $lang != '' && $user != '' && $target != '') {
		//---
		add_to_db($mdtitle, $type, $cat, $lang, $user, $target, $pupdate);
		//---
	};
};
//---
$cats = "";
//---
$qqq = quary2('select category, display from categories;');
//---
foreach ($qqq AS $Key => $ta ) {
	$ca = $ta['category'];
	$ds = $ta['display'];
	if ($ca != '') $cats .= "<option value='$ca'>$ds</option>";
};
//---
$typies = "
	<select name='type[]%s' id='type[]%s' class='form-select'>
		<option value='lead'>Lead</option>
		<option value='all'>All</option>
	</select>";
//---
// 
//---
foreach ( range(1, 7) as $numb ) {
    //---
	$cats_line = "<select class='form-select' name='cat[]$numb' id='cat[]$numb'>$cats</select>";
	$type_line = sprintf($typies, $numb, $numb);
    //---
	echo "
	<tr>
	  <td data-order='$numb'>$numb</td>
	  <td>	<input size='15' class='mdtitles' name='mdtitle[]$numb' id='mdtitle[]$numb'/>	</td>
	  <td>	$cats_line	</td>
	  <td>	$type_line	</td>
	  <td>	<input size='10' class='useri' name='user[]$numb' id='user[]$numb'/>	</td>
	  <td>	<input size='2' name='lang[]$numb' id='lang[]$numb'/>	</td>
	  <td>	<input size='20' name='target[]$numb' id='target[]$numb'/>	</td>
	  <td>	<input size='10' name='pupdate[]$numb' id='pupdate[]$numb'/>	</td>
	</tr>";
};
//---
?>
</tbody>
</table>
  <button type="submit" class="btn btn-success">send</button>
</form>
<?PHP

$script = '
<script>
$( function() {
    var availableTags = [
      %s
    ];
    $( ".useri" ).autocomplete({
      source: availableTags
    });
    
});
</script>';
//---
$ka = '';
//---
foreach(quary2('SELECT DISTINCT user from pages;') as $k => $tab) {
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
	var ur = 'cash/RTT.json';
    $( ".mdtitles" ).autocomplete({
	source: function (request, response){
		$.ajax({url: ur ,dataType: "json",data:{term: request.term,},success: function (data) {response(data.list);}});
	}
    });
});
</script> 
-->
</div>
</div>