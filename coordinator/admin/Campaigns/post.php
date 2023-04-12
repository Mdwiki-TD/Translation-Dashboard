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
if (isset($_POST['cat'])) {
	for($i = 0; $i < count($_POST['cat']); $i++ ){
		$cat = $_POST['cat'][$i];
		$dis = $_POST['dis'][$i];
		$ido = $_POST['id'][$i];
		$ido = (isset($ido)) ? $ido : '';
		$dep = $_POST['dep'][$i];
		//---
		$qua = "INSERT INTO categories (category, display, depth) SELECT '$cat', '$dis', '$dep'
		WHERE NOT EXISTS (SELECT 1 FROM categories WHERE category = '$cat')";
		//---
		if ($ido != '') {
			$qua = "UPDATE categories 
			SET 
			display = '$dis',
			category = '$cat',
			depth = '$dep'
			WHERE id = '$ido'
			";
		};
		execute_query($qua);
	};
};
//---
?>