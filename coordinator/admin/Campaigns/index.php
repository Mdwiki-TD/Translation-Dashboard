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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'post.php';
}
//---
echo <<<HTML
<div class='card-header'>
	<h4>Campaigns:</h4>
</div>
<div class='card-body'>
<form action="coordinator.php?ty=Campaigns" method="POST">
	<input name='ty' value="Campaigns" hidden/>
		<div class="form-group">
			<table class='table table-striped compact table-mobile-responsive table-mobile-sided' style='width: 90%;'>
				<thead>
					<tr>
						<th>#</th>
						<th>Category</th>
						<th>Campaign</th>
						<th>Depth</th>
						<th>Default Cat</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody id="tab_logic">
HTML;
//---
$uuux = '';
//---
// ALTER TABLE `categories` ADD `depth` INT(2) NULL DEFAULT NULL AFTER `display`;
// ALTER TABLE categories DROP depth;
//---
$qq = execute_query('select id, category, display, depth, def from categories;');
//---
$numb = 0;
//---
foreach ( $qq AS $Key => $table ) {
	$numb += 1;
	$id 		= $table['id'];
	$category 	= $table['category'];
	$display 	= $table['display'];
	$depth		= $table['depth'];
    //---
	$checked    = ($table['def'] == 1) ? 'checked' : '';
    //---
	echo <<<HTML
	<tr>
		<th data-content="#">$numb</th>
		<td data-content="Category">
			<input size='25' name='cats[]$numb' value='$category'/>
			<input name='id[]$numb' value='$id' hidden/>
		</td>
		<td data-content="Campaign">
			<input size='25' name='dis[]$numb' value='$display'/>
		</td>
		<td data-content="Depth">
			<input class='w-auto' type='number' name='dep[]$numb' value='$depth' min='0' max='10'/>
		</td>
		<td data-content="Default Cat">
			<input type='radio' class='form-check-input' id='default_cat' name='default_cat' value='$category' $checked>
		</td>
		<td data-content="Delete">
			<input type='checkbox' name='del[]$numb' value='$id'/> <label>delete</label>
		</td>
	</tr>
	HTML;
};
//---
?>

</tbody>
</table>
  <button type="submit" class="btn btn-success">Submit</button>
</form>
<span role='button' id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
</div>
<script type="text/javascript">
var i = 1;
function add_row() {
	var ii = $('#tab_logic >tr').length + 1;
	var e = "<tr>";
	e = e + "<td>" + ii + "</td>";
	e = e + "<td><input name='cats[]" + ii + "' placeholder='catname'/></td>";
	e = e + "<td><input name='dis[]" + ii + "' placeholder='display'/></td>";
	e = e + "<td><input name='dep[]" + ii + "' value='0'/></td>";
	e = e + "<td></td>";
	e = e + "<td></td>";
	e = e + "</tr>";

	$('#tab_logic').append(e);
	i++;
};
</script>
</div>