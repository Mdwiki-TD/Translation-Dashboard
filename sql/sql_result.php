<?php
//---
use function Actions\MdwikiSql\execute_query;

function make_sql_result($qua, $raw)
{
    //---
    $start_time = microtime(true);
    //---
    $uu = execute_query($qua);
    //---
    $end_time = microtime(true);
    //---
    $execution_time = $end_time - $start_time;
    $execution_time = number_format($execution_time, 2);
    //---
    $start = <<<HTML
    <table class="table table-striped sortable">
        <thead>
            <tr>
                <th>#</th>
    HTML;
    $text = '';
    //---
    $number = 0;
    //---
    foreach ($uu as $id => $row) {
        $number = $number + 1;
        $tr = '';
        //---
        foreach ($row as $nas => $value) {
            // if (!empty($nas)) {
            if (!preg_match('/^\d+$/', $nas, $m)) {
                $tr .= "<td>$value</th>";
                if ($number == 1) {
                    $start .= "<th class='text-nowrap'>$nas</th>";
                };
            };
        };
        //---
        if (!empty($tr)) {
            $text .= "<tr><td>$number</td>$tr</tr>";
        };
        //---
    };
    //---
    $start .= <<<HTML
        </tr>
        </thead>
    HTML;
    //---
    if (empty($raw)) {
        //---
        echo "<h4>sql results:$number. (time:$execution_time)</h4>";
        //---
        echo $start . $text . '</table>';
        //---
        if (global_test != '') var_export($uu);
        //---
        if (empty($text)) {
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
        foreach ($uu as $id => $row) {
            $ff = array();
            $n = $n + 1;
            //---
            foreach ($row as $nas => $value) {
                if (!preg_match('/^\d+$/', $nas, $m)) $ff[$nas] = $value;
            };
            //---
            $sql_result[$n] = $ff;
        };
        echo json_encode($sql_result);
        //---
        if ($raw == '66') {
            echo '<script>window.close();</script>';
        };
        //---

    };
    //---
};
//---
