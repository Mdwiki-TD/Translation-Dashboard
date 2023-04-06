<?php

function get_url_with_params( $params ) {
	//---
    $endPoint = "https://"."mdwiki.org/w/api.php";
    $url = $endPoint . "?" . http_build_query( $params );
	//---
    test_print("<br>get_url_with_params:$url<br>");
	//---
	$output = file_get_contents($url);
	//---
    $result = json_decode( $output, true );
    //---
    return $result;
};
//---
?>