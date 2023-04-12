<?php
//---
$new_q = "INSERT INTO users (username, email, wiki, user_group) SELECT DISTINCT user, '', '', '' from pages
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = user)";
//---
if (isset($_POST['del'])) {
	for($i = 0; $i < count($_POST['del']); $i++ ) {
		$del	= $_POST['del'][$i];
		//---
		if ($del != '') {
			$qu = "DELETE FROM users WHERE user_id = '$del'";
			execute_query($qu);
		};
	};
};
//---
if (isset($_POST['username'])) {
	for($i = 0; $i < count($_POST['username']); $i++ ){
		//---
		$user_name 	= $_POST['username'][$i];
		$email 	= $_POST['email'][$i];
		$ido 	= $_POST['id'][$i];
		$ido 	= (isset($ido)) ? $ido : '';
		$wiki 	= $_POST['wiki'][$i];
		$project 	= $_POST['project'][$i];
		// $project 	= '';
		//---
		if ($user_name != '') {
			//---
			$qua = "INSERT INTO users (username, email, wiki, user_group) SELECT '$user_name', '$email', '$wiki', '$project'
			WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = '$user_name')";
			//---	
			if ($ido != '' && $ido != 0 && $ido != "0") {
				$qua = "UPDATE `users` SET
				`username` = '$user_name',
				`email` = '$email',
				`user_group` = '$project',
				`wiki` = '$wiki'
				WHERE `users`.`user_id` = $ido;
				";
			};
			//---
			execute_query($qua);
			//---
		};
	};
};
//---
?>