<?php
//---
function make_sql_result($qua, $raw) {
	$uu = execute_query($qua);
    //---
    $start = '<table class="table table-striped soro2">
    <thead>
        <tr>
            <th>#</th>
    
    ';
    $text = '';
    //---
    $number = 0;
    //---
    foreach ( $uu AS $id => $row ) {
        $number = $number + 1;
        $tr = '';
        //---
        foreach ( $row AS $nas => $value ) {
            // if ($nas != '') {
            if (!preg_match( '/^\d+$/', $nas, $m ) ) {
                $tr .= "<td>$value</th>";
                if ($number == 1) { 
                    $start .= "<th class='text-nowrap'>$nas</th>";
                };
            };
        };
        //---
        if ($tr != '' ) { $text .= "<tr><td>$number</td>$tr</tr>"; };
        //---
    };
    //---
    $start .= '</tr>
    </thead>';
    //---
    if ( $raw == '' ) {
        //---
        echo "<h4>sql results:$number.</h4>";
        //---
        echo $start . $text . '</table>';
        //---
        if (global_test != '') var_export($uu);
        //---
        if ($text == '') {
            if (global_test != '') {
                print_r($uu);
            } else {
                echo var_dump($uu);
            };
        };
    } else {
        //---
        $sql_result = array();
        //---
        $n = 0;
        //---
        foreach ( $uu AS $id => $row ) {
            $ff = array();
            $n = $n + 1 ;
            //---
            foreach ( $row AS $nas => $value ) {
                if (!preg_match( '/^\d+$/', $nas, $m ) ) $ff[$nas] = $value;
            };
            //---
            $sql_result[$n] = $ff;
        };
        echo json_encode($sql_result);
		//---
		if ( $raw == '66' ) {
			echo '<script>window.close();</script>';
			
		};
		//---
        
    };
    //---
};
//---
?>