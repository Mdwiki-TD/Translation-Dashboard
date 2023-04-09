<?php
//---
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
?>
<div class='card-header'>
	<h4>Projects:</h4>
</div>
<div class='card-body'>
<form action="coordinator.php?ty=projects" method="POST">
	<input name='ty' value="projects" hidden/>
	  <div class="form-group">
		<table class='table compact' style="width:50%;">
			<tr>
				<th>Id</th>
				<th>Project</th>
				<th>Delete</th>
			</tr>
			<tbody id="g_tab">
<?php
//---
if (isset($_POST['del'])) {
	for($i = 0; $i < count($_POST['del']); $i++ ) {
		$del	= $_POST['del'][$i];
		//---
		if ($del != '') {
			$qua2 = "DELETE FROM projects WHERE g_id = '$del'";
			execute_query($qua2);
		};
	};
};
//---
if (isset($_POST['g_title'])) {
	for($i = 0; $i < count($_POST['g_title']); $i++ ) {
		//---
		$g_id  		= $_POST['g_id'][$i];
		$g_title	= $_POST['g_title'][$i];
		//---
		if ($g_title == '') continue;
		//---
		insert_to_projects($g_title, $g_id);
		//---
	};
};
//---
$numb = 0;
//---
foreach ( execute_query('select g_id, g_title from projects;') AS $g_title => $tab ) {
	$numb += 1;
    //---
	$g_id = $tab['g_id'];
	$g_title = $tab['g_title'];
    //---
	echo <<<HTML
	<tr>
		<td>
			<span><b>$numb</b></span>
			<input name='g_id[]$numb' value='$g_id' hidden/>
		</td>
	  	<td>
	  		<input name='g_title[]$numb' value='$g_title'/>
		</td>
	  	<td>
	  		<input type='checkbox' name='del[]$numb' value='$g_id'/> <label> delete</label>
	  	</td>
	</tr>
	HTML;
};
//---
?>
</tbody>
</table>
  <button type="submit" class="btn btn-success">send</button>
</form>
<span role='button' id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
</div>
<script type="text/javascript">
var i = 1;
function add_row() {
	var ii = $('#g_tab >tr').length + 1;
	var e = "<tr>";
	e = e + "<td>" + ii + "<input name='g_id[]' value='0' hidden/></td>";
	e = e + "<td><input name='g_title[]" + ii + "'/></td>";
	e = e + "<td></td>";
	e = e + "</tr>";
	$('#g_tab').append(e);
	i++;
};
</script>
</div>