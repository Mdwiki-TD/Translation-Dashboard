<?php
//---
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
use function Actions\MdwikiSql\fetch_query;
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	require __DIR__ . '/post.php';
}
//---
include_once 'Tables/tables.php';
//---
echo <<<HTML
<style>
	.ui-menuxx {
		height: 200px;
	}
</style>
<div class='card-header'>
	<h4>Add translations:</h4>
</div>
<div class='cardbody'>
	<form action="coordinator.php?ty=add" method="POST">
		<input name='ty' value="add" hidden />
		<div class="form-group">
			<table class='table table-striped compact table-mobile-responsive table-mobile-sided' style='font-size:95%;'>
				<thead>
					<tr>
						<th>#</th>
						<th>Mdwiki Title</th>
						<th>Campaign</th>
						<th>Type</th>
						<th>User</th>
						<th>Language</th>
						<th>Target</th>
						<th>Publication date</th>
					</tr>
				</thead>
				<tbody id='g_tab'>
HTML;
//---
$cats = "";
//---
$qqq = fetch_query('select category, campaign from categories;');
//---
foreach ($qqq as $Key => $ta) {
	$ca = $ta['category'] ?? "";
	$ds = $ta['campaign'] ?? "";
	if (!empty($ca)) $cats .= "<option value='$ca'>$ds</option>";
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
	$cats_line = <<<HTML
		<select class='form-select catsoptions' name='cat[]$numb' data-bs-theme="auto">
			$cats
		</select>
	HTML;
	//---
	$type_line = sprintf($typies, $numb, $numb);
	//---
	$table .= <<<HTML
	<tr>
		<td data-order='$numb' data-content='#'>
			$numb
		</td>
		<td data-content='Mdwiki Title'>
			<input class="form-control" size='15' class='mdtitles' name='mdtitle[]$numb' required/>
		</td>
		<td data-content='Campaign'>
			$cats_line
		</td>
		<td data-content='Type'>
			$type_line
		</td>
		<td data-content='User'>
			<input class="form-control td_user_input" size='10' name='user[]$numb' required/>
		</td>
		<td data-content='Lang.'>
			<input class="form-control" size='2' name='lang[]$numb' required/>
		</td>
		<td data-content='Target'>
			<input class="form-control" size='20' name='target[]$numb' required/>
		</td>
		<td data-content='Publication date'>
			<input class="form-control" size='10' name='pupdate[]$numb' placeholder='YYYY-MM-DD' required/>
		</td>
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
<button type="submit" class="btn btn-outline-primary mb-10">Save</button>
</form>
<span role='button' id="add_row" class="btn btn-outline-primary" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
</div>

<script type="text/javascript">
	var i = 1;

	function add_row() {
		var options = $('.catsoptions').html();
		var ii = $('#g_tab >tr').length + 1;
		var e = "<tr>";
		e = e + "<td>" + ii + "</td>";
		e = e + "<td>	<input class='form-control' size='15' class='mdtitles' name='mdtitle[]" + ii + "' required/>	</td>";
		e = e + "<td><select class='form-select catsoptions' name='cat[]" + ii + "'>" + options + "</select></td>";
		e = e + "<td><select name='type[]%s' class='form-select'>";
		e = e + "<option value='lead'>Lead</option><option value='all'>All</option></select></td>";
		e = e + "<td>	<input class='form-control' size='10' class='td_user_input' name='user[]" + ii + "' required/>	</td>";
		e = e + "<td>	<input class='form-control' size='2' name='lang[]" + ii + "' required/>	</td>";
		e = e + "<td>	<input class='form-control' size='20' name='target[]" + ii + "' required/>	</td>";
		e = e + "<td>	<input class='form-control' size='10' name='pupdate[]" + ii + "' required/>	</td>";
		e = e + "<td></td>";
		e = e + "</tr>";
		$('#g_tab').append(e);
		i++;
	};
</script>

<!--
<script>
$( function() {
	var ur = 'Tables/cats_cash/RTT.json';
    $( ".mdtitles" ).autocomplete({
	source: function (request, response){
		$.ajax({url: ur ,dataType: "json",data:{term: request.term,},success: function (data) {response(data.list);}});
	}
    });
});
</script>
-->
</div>
