<?php
//---
for($i = 0; $i < count($_POST['del']); $i++ ) {
	$del	= $_POST['del'][$i];
	//---
	if ($del != '') {
		$qu = "DELETE FROM users WHERE user_id = '$del'";
		quary2($qu);
	};
};
//---
for($i = 0; $i < count($_POST['username']); $i++ ){
	//---
	$username 	= $_POST['username'][$i];
	$email 	= $_POST['email'][$i];
	$ido 	= $_POST['id'][$i];
	$wiki 	= $_POST['wiki'][$i];
	//---
	if ($username != '') {
		//---
		$qua = "INSERT INTO users (username, email, wiki) SELECT '$username', '$email', '$wiki'
		WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = '$username')";
		//---	
		if (isset($ido)) {
			$qua = "UPDATE `users` SET 
			`username` = '$username', 
			`email` = '$email', 
			`wiki` = '$wiki' 
			WHERE `users`.`user_id` = $ido;
			";
		};
		//---
		quary2($qua);
		//---
	};
};
//---
$new_q = "INSERT INTO users (username, email, wiki) SELECT DISTINCT user, '', '' from pages
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = user)";
//---
// quary2($new_q);
//---
$nn = 0;
foreach(quary2('SELECT count(DISTINCT user) as c from pages;') as $k => $tab) $nn = $tab['c'];
//---
echo "<h4>Emails ($nn user):</h4>";
//---
?>


<form action="coordinator.php?ty=Emails" method="POST">
	<input name='ty' value="Emails" hidden/>
	  <div class="form-group">
		  <table id='em' class='table table-striped compact'>
			<thead>
			  <tr>
				<th>#</th>
				<th>Username</th>
				<th>Email</th>
				<th>Wiki</th>
				<th>Live</th>
				<th>Delete</th>
			  </tr>
			</thead>
			<tbody id="tab_ma">
<?PHP
//---
/*
CREATE TABLE users (
	user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL,
	wiki VARCHAR(255) NOT NULL
);
*/
// ALTER TABLE `users` ADD `depth` INT(2) NULL DEFAULT NULL AFTER `display`;
// ALTER TABLE users DROP depth;
//---
$qu2 = "select user_id, username, email, wiki, 
(select count(target) from pages WHERE target != '' and user = username) as live
from users, pages
where user = username
group by username  
ORDER BY live DESC;
";
//---
$qu1 = '
	select user_id, username, email, wiki 
	from users
	#ORDER BY email DESC
;';
//---
$qq = quary2($qu2);
//---
$numb = 0;
//---
foreach ( $qq AS $Key => $table ) {
	$numb += 1;
	$id 	= $table['user_id'];
	$username 	= $table['username'];
	$email 	= $table['email'];
	$wiki	= $table['wiki'];
	$live	= $table['live'];
    //---
	echo "
	<tr>
	  <td data-order='$numb'>$numb</td>
	  <td data-order='$username'>
	  	<span>$username</span>
	  	<input name='username[]$numb' id='username[]$numb' value='$username' hidden/>
	  	<input name='id[]$numb' id='id[]$numb' value='$id' hidden/>
	  </td>
	  <td data-order='$email'>
	  	<span style='display: none'>$email</span>
	  	<input size='25' name='email[]$numb' id='email[]$numb' value='$email'/>
	  </td>
	  <td data-order='$wiki'>
	  	<input size='10' name='wiki[]$numb' id='wiki[]$numb' value='$wiki'/>
	  </td>
	  <td data-order='$live'>
	  	<span>$live</span>
	  </td>
	  <td><input type='checkbox' name='del[]$numb' value='$id'/> <label>delete</label></td>
	</tr>";
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
var i = 1;
function add_row() {
	var ii = $('#tab_ma >tr').length + 1;
	var e = "<tr><td><input name='username[]" + ii + "'/></td><td><input name='email[]" + ii + "'/></td><td><input name='wiki[]" + ii + "'/></td><td></td></tr>";

	$('#tab_ma').append(e);
	i++;
};

$(document).ready( function () {
	var table = $('#em').DataTable({
    // paging: false,
	lengthMenu: [[50, 100], [50, 100]],
    // scrollY: 800
	});
} );

</script>
  </div>