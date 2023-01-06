
<?php
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
?>

<h4>Emails:</h4>
<form action="coordinator.php?ty=Emails" method="POST">
	<input name='ty' value="Emails" hidden/>
	  <div class="form-group">
		  <table id='em' class='table table-striped compact'>
			<thead>
			  <tr>
				<th>Username</th>
				<th>Email</th>
				<th>Wiki</th>
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
$qq = quary2('
	select user_id, username, email, wiki 
	from users
	#ORDER BY email DESC
;');
//---
$numb = 0;
//---
foreach ( $qq AS $Key => $table ) {
	$numb += 1;
	$id 	= $table['user_id'];
	$username 	= $table['username'];
	$email 	= $table['email'];
	$wiki	= $table['wiki'];
    //---
	$qu = "DELETE FROM users WHERE user_id = '$id'";
    $qua = rawurlencode( $qu );
    $urle = "sql.php?code=$qua&pass=$sqlpass&raw=66";
    //---
	echo "
	<tr>
	  <td data-order='$username'>
	  	<span>$username</span>
	  	<input name='username[]$numb' id='username[]$numb' value='$username' hidden/>
	  	<input name='id[]$numb' id='id[]$numb' value='$id' hidden/>
	  </td>
	  <td data-order='$email'>
	  	<span style='display: none'>$email</span>
	  	<input name='email[]$numb' id='email[]$numb' value='$email'/>
	  </td>
	  <td data-order='$wiki'>
	  	<input name='wiki[]$numb' id='wiki[]$numb' value='$wiki'/>
	  </td>
	  <td>
	  	<a href='$urle' target='_blank' onclick='refr1()'>delete</a>
	  </td>
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
function refr1() {
	setTimeout(function(){
   window.location.reload(1);
}, 1000);
}

$(document).ready( function () {
    
	var table = $('#em').DataTable({
    paging: false,
    scrollY: 400
	});
	var data = table
		.column( 2 )
		.data()
		.sort();
} );

</script>
  </div>