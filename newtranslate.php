<?php
//--------------------
function start_trans_py($title,$test,$fixref,$tra_type) {
	//--------------------
	$title2 = $title;
	$title2 = rawurlencode(str_replace ( ' ' , '_' , $title2 ) );
	//--------------------
	$dd = "python3 translate.py -title:$title2" ;
	if ($fixref != '' ) $dd = $dd . ' fixref';
	//--------------------	
	if ($tra_type == 'all' ) $dd = $dd . ' wholearticle';

	//--------------------	
	if ($test != "") { print $dd . '<br>'; } ; 
	//--------------------	
	$command = escapeshellcmd( $dd );
	$output = shell_exec($command);
	//--------------------	
	return $output;
};
//--------------------
function start_trans_php($title,$test,$fixref) {
	//--------------------
	$title2 = $title;
	$title2 = rawurlencode(str_replace ( ' ' , '_' , $title2 ) );
	//--------------------
	$dd = "python3 translate.py -title:$title2" ;
	if ($fixref != '' ) $dd = $dd . ' fixref';
	
	if ($test != "") { 
		$dd .= ' test ';
		print $dd . '<br>';
	} ; 
	
	$command = escapeshellcmd( $dd );
	$output = shell_exec($command);
	return $output;
};
//--------------------
//--------------------
?>