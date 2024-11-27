<?php
//---
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
use function Actions\Html\make_mail_icon;
use function Actions\Html\make_project_to_user;
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
function make_edit_icon($id, $user, $email, $wiki2, $project) {
	//---
    $edit_params = array(
		'id'   => $id,
		'nonav'  => 1,
		'user'  => $user,
		'email'  => $email,
		'wiki'  => $wiki2,
		'project'  => $project
	);
    //---
    $edit_url = "coordinator.php?ty=Emails/edit_user&" . http_build_query( $edit_params );
    //---
	$onclick = 'pupwindow1("' . $edit_url . '")';
    //---
    return <<<HTML
    	<a class='btn btn-outline-primary btn-sm' onclick='$onclick'>Edit</a>
    HTML;
}
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
	foreach ( fetch_query($last_qua) AS $Key => $gg ) {
		if(!in_array($gg['user'], $last_user_to_tab)) $last_user_to_tab[$gg['user']] = $gg;
	};
	//---
	// print_r(json_encode($last_user_to_tab));
};
//---
// $nn = 0;
// foreach(fetch_query('SELECT count(DISTINCT user) as c from pages;') as $k => $tab) $nn = $tab['c'] ?? "";
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
			<table id='em' class='table table-striped compact table-mobile-responsive table-mobile-sided'>
				<thead>
					<tr>
						<th>#</th>
						<th>Username</th>
						<th>Email</th>
						<th></th>
						<th>Project</th>
						<th>Wiki</th>
						<th>Live</th>
						<th>Edit</th>
						<!-- <th>Delete</th> -->
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
		p1.user, count(target) as live
		from pages p1
		where p1.target != ''
		group by p1.user
		order by live desc
	SQL;
	//---
	foreach ( fetch_query($q_live) AS $Key => $gg ) {
		$live_pages[$gg['user']] = number_format($gg['live']);
	};
	//---
	// print_r(json_encode($live_pages));
	//---
};
//---
$users_done = array();
//---
foreach ( fetch_query("select user_id, username, email, wiki, user_group from users;") AS $Key => $gk ) {
    $users_done[$gk['username']] = $gk;
};
//---
$qu1 = <<<SQL
	select DISTINCT user from pages
	WHERE NOT EXISTS (SELECT 1 FROM users WHERE user = username)
SQL;
//---
foreach ( fetch_query($qu1) AS $d => $tat ) if (!in_array($tat['user'], $users_done)) {
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
	$id			= $table['user_id'] ?? "";
	$email 		= $table['email'] ?? "";
	$wiki		= $table['wiki'] ?? "";
	$wiki2		= $wiki . "wiki";
	$project	= $table['user_group'] ?? "";
    //---
	$project_line = make_project_to_user($projects_title_to_id, $project);
	//---
	$user 		= $table['username'] ?? "";
	$mail_icon = '';
	if (in_array($user_name, array_keys($last_user_to_tab))) {
		$mail_icon = make_mail_icon($last_user_to_tab[$user_name]);
	} else {
	};
	//---
	$edit_icon = make_edit_icon($id, $user, $email, $wiki, $project);
	//---
	echo <<<HTML
	<tr>
		<td data-order='$numb' data-content='#'>
			$numb
		</td>
		<td data-order='$user_name' data-content='User name'>
			<span><a href='leaderboard.php?user=$user_name'>$user_name</a></span>
			<input name='username[]$numb' value='$user_name' hidden/>
			<input name='id[]$numb' value='$id' hidden/>
		</td>
		<td data-order='$email' data-search='$email' data-content='Email'>
			<input class='form-control' size='25' name='email[]$numb' value='$email'/>
		</td>
		<td data-content=''>
			$mail_icon
		</td>
		<td data-order='$project' data-search='$project' data-content='Project'>
			<select name='project[]$numb' class='form-select options'>$project_line</select>
		</td>
		<td data-order='$wiki' data-search='$wiki2' data-content='Wiki'>
			<input class='form-control' size='4' name='wiki[]$numb' value='$wiki'/>
		</td>
		<td data-order='$live' data-content='Live'>
			<span>$live</span>
		</td>
		<td data-content='Edit'>
			<span>$edit_icon</span>
		</td>
		<!-- <td data-content='Delete'>
			<input type='checkbox' name='del[]$numb' value='$id'/> <label>delete</label>
		</td> -->
	</tr>
	HTML;
};
//---
?>

			</tbody>
		</table>
		<button type="submit" class="btn btn-outline-primary">Save</button>
		<span role='button' id="add_row" class="btn btn-outline-primary" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
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
		// find if any element has attr selected and unselect it
		options = options.replace(/selected/g, '');
		var e = "<tr>";
		e = e + "<td>" + ii + "</td>";
		e = e + "<td><input class='form-control' name='username[]" + ii + "'/></td>";
		e = e + "<td><input class='form-control' size='25' name='email[]" + ii + "'/></td>";
		e = e + "<td>-</td>";
		e = e + "<td><select name='project[]" + ii + "' class='form-select'> " + options + "</select></td>";
		e = e + "<td><input class='form-control' size='4' name='wiki[]" + ii + "'/></td>";
		e = e + "<td>0</td>";
		e = e + "<td>-</td>";
		e = e + "</tr>";

		$('#tab_ma').append(e);
		i++;
	};

	$(document).ready( function () {
		var t = $('#em').DataTable({
		// order: [[5	, 'desc']],
		// paging: false,
		lengthMenu: [[50, 100, 150], [50, 100, 150]],
		// scrollY: 800
		});
	} );

</script>

</div>
