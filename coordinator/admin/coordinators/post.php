<?php
//---
if (isset($_POST['del'])) {
	for($i = 0; $i < count($_POST['del']); $i++ ) {
		$del	= $_POST['del'][$i];
		//---
		if ($del != '') {
			$qua2 = "DELETE FROM coordinator WHERE id = '$del'";
			execute_query($qua2);
		};
	};
};
//---
if (isset($_POST['user'])) {
	for($i = 0; $i < count($_POST['user']); $i++ ) {
		$ido  	= $_POST['id'][$i] ?? '';
		$user  	= $_POST['user'][$i] ?? '';
		//---
		$user = trim($user);
		//---
		if ($user != '' && $ido == '') {
			$qua = "INSERT INTO coordinator (user) SELECT '$user' WHERE NOT EXISTS (SELECT 1 FROM coordinator WHERE user = '$user')";
			//---
			execute_query($qua);
		};
	};
};
//---
?>