<?php
//---
/*
(?:name|id)=(['"])(.*?)\1 (?:name|id)=\1\2\1
name=$1$2$1
*/
//---
$last_user_to_tab = array();
if (true) {
	$last_qua = <<<SQL
		select DISTINCT p1.target, p1.title, p1.cat, p1.user, p1.pupdate, p1.lang
		from pages p1
		where target != ''
		and p1.pupdate = (select p2.pupdate from pages p2 where p2.user = p1.user ORDER BY p2.pupdate DESC limit 1)
		group by p1.user
		ORDER BY p1.pupdate DESC
	SQL;
	//---
	foreach ( execute_query($last_qua) AS $Key => $gg ) {
		if(!in_array($gg['user'], $last_user_to_tab)) $last_user_to_tab[$gg['user']] = $gg;
	};
	//---
	// print_r(json_encode($last_user_to_tab));
};
//---
// $nn = 0;
// foreach(execute_query('SELECT count(DISTINCT user) as c from pages;') as $k => $tab) $nn = $tab['c'];
//---<h4>Emails ($nn user):</h4>
//---
echo <<<HTML
	<div class='card-header'>
	<h4>Emails:</h4>
	</div>
	<div class='card-body'>
	<form action="coordinator.php?ty=Emails" method="POST">
		<input name='ty' value="Emails" hidden/>
		<div class="form-group">
			<table id='em' class='table table-striped compact'>
				<thead>
				<tr>
					<th>#</th>
					<th>Username</th>
					<th>Email</th>
					<th></th>
					<th>Project</th>
					<th>Wiki</th>
					<th>Live</th>
					<th>Delete</th>
				</tr>
				</thead>
				<tbody id="tab_ma">
	HTML;
//---
$live_pages = array();
//---
if (true) {
	$q_live = <<<SQL
		select DISTINCT 
		p1.user, (select count(target) from pages p2 where p2.user = p1.user and p2.target != '') as live
		from pages p1
		group by p1.user;
	SQL;
	//---
	foreach ( execute_query($q_live) AS $Key => $gg ) {
		$live_pages[$gg['user']] = number_format($gg['live']);
	};
	//---
	// print_r(json_encode($live_pages));
	//---
};
//---
$users_done = array();
//---
foreach ( execute_query("select user_id, username, email, wiki, user_group from users;") AS $Key => $gk ) {
    $users_done[$gk['username']] = $gk;
};
//---
$qu1 = <<<SQL
	select DISTINCT user from pages 
	WHERE NOT EXISTS (SELECT 1 FROM users WHERE user = username)
SQL;
//---
foreach ( execute_query($qu1) AS $d => $tat ) if (!in_array($tat['user'], $users_done)) {
    $users_done[$tat['user']] = array( 'user_id' => 0, 'username' => $tat['user'], 'email' => '', 'wiki' => '', 'user_group' => '');
}
//---
$sorted_array = array();
foreach ( $users_done AS $u => $tab ) {
	$sorted_array[$u] = $live_pages[$u] ?? 0;
};
arsort($sorted_array);
//---
$numb = 0;
//---
foreach ( $sorted_array as $user_name => $d) {
	//---
	$numb += 1;
	//---
	$table = $users_done[$user_name];
	//---
	$live		= $live_pages[$user_name] ?? 0;
	//---
    // print_r(json_encode($table));
	//---
	$id			= $table['user_id'];
	$email 		= $table['email'];
	$wiki		= $table['wiki'];
	$wiki2		= $wiki . "wiki";
	$project	= $table['user_group'];
    //---
	$project_line = make_project_to_user($projects_title_to_id, $project);
	//---
	$user 		= $table['username'];
	$mail_icon = '';
	if (in_array($user_name, array_keys($last_user_to_tab))) {
		$mail_icon = make_mail_icon($last_user_to_tab[$user_name]);
	} else {
	};
	//---
	echo <<<HTML
	<tr>
		<td data-order='$numb'>$numb</td>
		<td data-order='$user_name'>
			<span><a href='leaderboard.php?user=$user_name'>$user_name</a></span>
			<input name='username[]$numb' value='$user_name' hidden/>
			<input name='id[]$numb' value='$id' hidden/>
		</td>
		<td data-order='$email' data-search='$email'>
			<input size='25' name='email[]$numb' value='$email'/>
		</td>
		<td>
			$mail_icon
		</td>
		<td data-order='$project' data-search='$project'>
			<select name='project[]$numb' class='form-select options'>$project_line</select>
		</td>
		<td data-order='$wiki' data-search='$wiki2'>
			<input size='4' name='wiki[]$numb' value='$wiki'/>
		</td>
		<td data-order='$live'>
			<span>$live</span>
		</td>
		<td><input type='checkbox' name='del[]$numb' value='$id'/> <label>delete</label></td>
	</tr>
	HTML;
};
//---
?>

			</tbody>
		</table>
		<button type="submit" class="btn btn-success">Submit</button>
		<span role='button' id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
	  </div>
</form>

<script type="text/javascript">

	function pupwindow(url) {
		window.open(url, 'popupWindow', 'width=850,height=550,scrollbars=yes');
	};

	var i = 1;
	function add_row() {
		var ii = $('#tab_ma >tr').length + 1;
		
		var options = $('.options').html();
		var e = "<tr>";
		e = e + "<td>" + ii + "</td>";
		e = e + "<td><input name='username[]" + ii + "'/></td>";
		e = e + "<td><input size='25' name='email[]" + ii + "'/></td>";
		e = e + "<td><select name='project[]" + ii + "' class='form-select'> " + options + "</select></td>";
		e = e + "<td><input size='4' name='wiki[]" + ii + "'/></td>";
		e = e + "<td>0</td>";
		e = e + "<td></td>";
		e = e + "</tr>";

		$('#tab_ma').append(e);
		i++;
	};

	$(document).ready( function () {
		var t = $('#em').DataTable({
		// order: [[5	, 'desc']],
		// paging: false,
		lengthMenu: [[25, 50, 100], [25, 50, 100]],
		// scrollY: 800
		});
	} );

</script>

</div>