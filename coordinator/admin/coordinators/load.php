<div class='card-header'>
	<h4>Coordinators:</h4>
</div>
<div class='card-body'>
<form action="coordinator.php?ty=coordinators" method="POST">
	<input name='ty' value="coordinators" hidden/>
	  <div class="form-group">
		<table class='table compact' style="width:50%;">
			<tr>
				<th>id</th>
				<th>User</th>
				<th>Delete</th>
			</tr>
			<tbody id="coo_tab">
<?php
//---
$qq = execute_query('select id, user from coordinator;');
//---
$numb = 0;
//---
foreach ( $qq AS $Key => $table ) {
	$numb += 1;
	$ide	= $table['id'];
	$usere	= $table['user'];
    //---
	echo "
	<tr>
		<td>
		<span><b>$ide</b></span>
	  	<input name='id[]$numb' value='$ide' hidden/>
	  </td>
	  <td>
		<span><a href='leaderboard.php?user=$usere'>$usere</a></span>
	  	<input name='user[]$numb' value='$usere' hidden/>
		</td>
	  <td>
	  	<input type='checkbox' name='del[]$numb' value='$ide'/> <label> delete</label>
	  </td>
	</tr>";
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
	var ii = $('#coo_tab >tr').length + 1;
	var e = "<tr>";
	e = e + "<td>" + ii + "</td>";
	e = e + "<td><input name='user[]" + ii + "'/></td>";
	e = e + "<td></td>";
	e = e + "</tr>";
	$('#coo_tab').append(e);
	i++;
};
</script>
</div>