<style>
	.ui-menuxx {
		height: 200px;
	}
</style>
<?php
//---
require('tables.php');
//---
?>
<div class='card-header'>
	<h4>Add translations:</h4>
</div>
<div class='cardbody'>
	<form action="coordinator.php?ty=add" method="POST">
		<input name='ty' value="add" hidden />
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
					$cats = "";
					//---
					$qqq = execute_query('select category, display from categories;');
					//---
					foreach ($qqq as $Key => $ta) {
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
					foreach (range(1, 1) as $numb) {
						//---
						$cats_line = "<select class='form-select catsoptions' name='cat[]$numb'>$cats</select>";
						$type_line = sprintf($typies, $numb, $numb);
						//---
						$table .= <<<HTML
	<tr>
	  <td data-order='$numb'>$numb</td>
	  <td> <input size='15' class='mdtitles' name='mdtitle[]$numb' required/> </td>
	  <td> $cats_line </td>
	  <td> $type_line </td>
	  <td> <input size='10' class='useri' name='user[]$numb' required/> </td>
	  <td> <input size='2' name='lang[]$numb' required/> </td>
	  <td> <input size='20' name='target[]$numb' required/>	</td>
	  <td> <input size='10' name='pupdate[]$numb' required/> </td>
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
		e = e + "<td>	<input size='15' class='mdtitles' name='mdtitle[]" + ii + "' required/>	</td>";
		e = e + "<td><select class='form-select catsoptions' name='cat[]" + ii + "'>" + options + "</select></td>";
		e = e + "<td><select name='type[]%s' class='form-select'>";
		e = e + "<option value='lead'>Lead</option><option value='all'>All</option></select></td>";
		e = e + "<td>	<input size='10' class='useri' name='user[]" + ii + "' required/>	</td>";
		e = e + "<td>	<input size='2' name='lang[]" + ii + "' required/>	</td>";
		e = e + "<td>	<input size='20' name='target[]" + ii + "' required/>	</td>";
		e = e + "<td>	<input size='10' name='pupdate[]" + ii + "' required/>	</td>";
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
foreach (execute_query('SELECT DISTINCT user from pages;') as $k => $tab) {
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