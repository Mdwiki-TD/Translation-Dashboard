<?php
//------------------
function start_trans_py($title,$test,$fixref) {
	//--------------------------------------
	$title2 = $title;
	$title2 = rawurlencode(str_replace ( ' ' , '_' , $title2 ) );
	//------------------
	$dd = "python3 trans.py -title:$title2" ;
	if ($fixref != '' ) $dd = $dd . ' fixref';
	
	if ($test != "") { print $dd . '<br>'; } ; 
	
	$command = escapeshellcmd( $dd );
	$output = shell_exec($command);
	return $output;
};
//------------------
function start_trans_php($title,$test,$fixref) {
	//--------------------------------------
	$title2 = $title;
	$title2 = rawurlencode(str_replace ( ' ' , '_' , $title2 ) );
	//------------------
	$dd = "python3 trans.py -title:$title2" ;
	if ($fixref != '' ) $dd = $dd . ' fixref';
	
	if ($test != "") { print $dd . '<br>'; } ; 
	
	$command = escapeshellcmd( $dd );
	$output = shell_exec($command);
	return $output;
};
//------------------
//------------------
?>