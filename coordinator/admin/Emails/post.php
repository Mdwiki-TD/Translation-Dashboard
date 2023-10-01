<?php
//---
$new_q = "INSERT INTO users (username, email, wiki, user_group, reg_date) SELECT DISTINCT user, '', '', '', now() from pages
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = user)";
//---
if (isset($_POST['del'])) {
	var_export($_POST['del']);
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
			$user_name = trim($user_name);
			$email     = trim($email);
			$wiki      = trim($wiki);
			$project   = trim($project);
			//---
			sql_add_user($user_name, $email, $wiki, $project, $ido);
			//---
		};
	};
};
//---
?>