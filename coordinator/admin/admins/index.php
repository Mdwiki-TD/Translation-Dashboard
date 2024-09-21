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
echo <<<HTML
	<div class='card-header'>
		<h4>Coordinators:</h4>
	</div>
	<div class='card-body'>
		<form action="coordinator.php?ty=admins" method="POST">
			<input name='ty' value="admins" hidden/>
			<div class="form-group">
				<table class='table table-striped compact table-mobile-responsive table-mobile-sided' style="width:50%;">
					<thead>
						<tr>
							<th>id</th>
							<th>User</th>
							<th>Delete</th>
						</tr>
					</thead>
					<tbody id="coo_tab">
HTML;
//---
$qq = fetch_query('select id, user from coordinator;');
//---
$numb = 0;
//---
foreach ( $qq AS $Key => $table ) {
	$numb += 1;
	$ide	= $table['id'] ?? "";
	$usere	= $table['user'] ?? "";
    //---
	echo <<<HTML
		<tr>
			<td data-content="id">
				<span><b>$ide</b></span>
				<input name='id[]$numb' value='$ide' hidden/>
			</td>
			<td data-content="user">
				<span><a href='leaderboard.php?user=$usere'>$usere</a></span>
				<input name='user[]$numb' value='$usere' hidden/>
			</td>
			<td data-content="delete">
				<input type='checkbox' name='del[]$numb' value='$ide'/> <label> delete</label>
			</td>
		</tr>
	HTML;
};
//---
?>
					</tbody>
				</table>
				<button type="submit" class="btn btn-outline-primary">Save</button>
				<span role='button' id="add_row" class="btn btn-outline-primary" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
			</form>
		</div>
	</div>
	<script type="text/javascript">
	var i = 1;
	function add_row() {
		var ii = $('#coo_tab >tr').length + 1;
		var e = "<tr>";
		e = e + "<td>" + ii + "</td>";
		e = e + "<td><input class='form-control' name='user[]" + ii + "'/></td>";
		e = e + "<td>-</td>";
		e = e + "</tr>";
		$('#coo_tab').append(e);
		i++;
	};
	</script>
</div>
