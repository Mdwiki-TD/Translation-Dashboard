<?php
//---
if (isset($_POST['del'])) {
	for($i = 0; $i < count($_POST['del']); $i++ ) {
		$del	= $_POST['del'][$i];
		//---
		if ($del != '') {
			$qua2 = "DELETE FROM categories WHERE id = '$del'";
			execute_query($qua2);
		};
	};
};
//---
$default_cat = $_POST['default_cat'] ?? '';
//---
if (isset($_POST['cats'])) {
	for($i = 0; $i < count($_POST['cats']); $i++ ){
		$cats= $_POST['cats'][$i];
		$dis = $_POST['dis'][$i];
		$ido = $_POST['id'][$i];
		$ido = (isset($ido)) ? $ido : '';
		$dep = $_POST['dep'][$i];
		//---
		// $def = $_POST['def'][$i];
		$def = ($default_cat == $cats) ? 1 : 0;
		//---
		$qua = "INSERT INTO categories (category, display, depth, def) SELECT '$cats', '$dis', '$dep', '$def'
		WHERE NOT EXISTS (SELECT 1 FROM categories WHERE category = '$cats')";
		//---
		if ($ido != '') {
			$qua = "UPDATE categories 
			SET 
			display = '$dis',
			category = '$cats',
			depth = '$dep',
			def = $def
			WHERE id = '$ido'
			";
		};
		//---
		if (isset($_REQUEST['test'])) {
			echo "<br>$qua<br>";
		};
		//---
		execute_query($qua);
	};
	if ($_REQUEST['test'] == 'dd') {
		exit;
	};
};
//---
if (isset($_POST['cat'])) {
	for($i = 0; $i < count($_POST['cat']); $i++ ){
		$cat = $_POST['cat'][$i];
		$dis = $_POST['dis'][$i];
		$def = $_POST['def'][$i];
		$ido = $_POST['id'][$i];
		$ido = (isset($ido)) ? $ido : '';
		$dep = $_POST['dep'][$i];
		//---
		$qua = "INSERT INTO categories (category, display, depth, def) SELECT '$cat', '$dis', '$dep', '$def'
		WHERE NOT EXISTS (SELECT 1 FROM categories WHERE category = '$cat')";
		//---
		if ($ido != '') {
			$qua = "UPDATE categories 
			SET 
			display = '$dis',
			category = '$cat',
			depth = '$dep',
			def = '$def'
			WHERE id = '$ido'
			";
		};
		execute_query($qua);
	};
};
//---
?>