<?php
/*
//---
delete table
DROP table views_by_month ;
//---
// add columns

ALTER TABLE `pages` ADD `add_date` VARCHAR(120) NULL DEFAULT NULL AFTER `target`;

//---
CREATE TABLE coordinator (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(120) NOT NULL
    )
//---
INSERT INTO categories (category, display) SELECT 'RTT', '';
CREATE TABLE categories (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(120) NOT NULL,
    display VARCHAR(120) NOT NULL
    )
//---
CREATE TABLE qids (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    qid VARCHAR(120) NULL
    )
//---
INSERT INTO qids (title, qid) SELECT DISTINCT p.title, '' from pages p WHERE NOT EXISTS (SELECT 1 FROM qids q WHERE q.title= p.title)
//---
CREATE TABLE pages (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    word INT(6) NULL,
    translate_type VARCHAR(20) NULL,
    cat VARCHAR(120) NULL,
    lang VARCHAR(30) NULL,
    date VARCHAR(120) NULL,
    user VARCHAR(120) NULL,
    pupdate VARCHAR(120) NULL,
    target VARCHAR(120) NULL
    )
//---
CREATE TABLE words (
    w_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    w_title VARCHAR(120) NOT NULL,
    w_lead_words INT(6) NULL,
    w_all_words INT(6) NULL
    )
*/
//---
include_once('td_config.php');
$ini = Read_ini_file('OAuthConfig.ini');
include_once('functions.php');
//---
$username2 = isset($_COOKIE['username']) ? $_COOKIE['username'] : $username;
//---
if (!in_array($username2, $usrs)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
$sqlpass = $ini['sqlpass'];
//---
$pass = isset($_REQUEST['pass']) ? $_REQUEST['pass'] : '';
$qua  = isset($_REQUEST['code']) ? $_REQUEST['code'] : '';
$raw  = isset($_REQUEST['raw']) ? $_REQUEST['raw'] : '';
$test = isset($_REQUEST['test']) ? $_REQUEST['test'] : '';
//---
if ( $raw == '' ) {
    require('header.php');
    //---
    $quu = "SELECT A.id from pages A, pages B where A.target = '' and A.lang = B.lang and A.title = B.title and B.target != '';";
    //---
    $quaa = $qua ? $qua : $quu ;
    //---    
    echo '

    <style>
    #code {
        font-family: Monaco, Consolas, "Ubuntu Mono", monospace;
        width: 100%;
        height: auto;
        min-height: 144px;
    }
    </style>
    ';
    //---
	$sql_php = "sql.php?pass=$pass&";
    //---
    echo "
    <div class='row'>
        <div class='col-md'>
            <ul>
                <li><a href='" . $sql_php . "code=show tables;'>show tables</a></li>
                <li><a href='" . $sql_php . "code=describe words;'>describe words;</a></li>
                <li><a href='" . $sql_php . "code=describe pages;'>describe pages;</a></li>
                </ul>
            </div>
            <div class='col-md'>
                <ul>
                <li><a href='" . $sql_php . "code=select * from words;'>select * from words;</a></li>
                <li><a href='" . $sql_php . "code=select * from pages;'>select * from pages;</a></li>
                <li><a href='" . $sql_php . "code=select * from qids;'>select * from qids;</a></li>
                <li><a href='" . $sql_php . "code=SELECT%20%0AA.id%20as%20id1%2C%20A.target%20as%20t1%2C%0AB.id%20as%20id2%2C%20B.target%20as%20t2%0AFROM%20views%20A%2C%20views%20B%0AWHERE%20A.target%20%3D%20B.target%0Aand%20A.lang%20%3D%20B.lang%0Aand%20A.id%20!%3D%20B.id%0A%3B'>Duplicte views.</a></li>
                </ul>
            </div>
            <div class='col-md'>
                <ul>
                <li><a href='" . $sql_php . "code=SELECT%20*%20from%20pages%20p1%20%0Awhere%20p1.target%20%3D%20%27%27%20and%20EXISTS%20%20(SELECT%201%20FROM%20pages%20p2%20WHERE%20p1.title%20%3D%20p2.title%20and%20p2.target%20!%3D%20%27%27%0Aand%20p1.lang%20%3D%20p2.lang%0A)'>Duplicte pages to remove.</a></li>
				<li><a href='" . $sql_php . "code=SELECT%20A.lang%20as%20lang%2CA.title%20as%20title%2C%20%0AA.id%20AS%20id1%2C%20A.user%20AS%20u1%2C%20A.target%20as%20T1%2C%20A.date%20as%20d1%2C%0AB.id%20AS%20id2%2C%20B.user%20AS%20u2%2C%20B.target%20as%20T2%2C%20B.date%20as%20d2%0A%0AFROM%20pages%20A%2C%20pages%20B%0AWHERE%20A.id%20%3C%3E%20B.id%0AAND%20A.title%20%3D%20B.title%0AAND%20A.lang%20%3D%20B.lang%0Aand%20A.target%20!%3D%20%27%27%0AORDER%20BY%20A.title%3B'>Duplicte pages2.</a></li>

                </ul>
            </div>
            <div class='col-md'>
                <ul>
                <li><a href='" . $sql_php . "code=SELECT+%0D%0AA.id+as+id1%2C+A.title+as+t1%2C+A.qid+as+q1%2C+%0D%0AB.id+as+id2%2C+B.title+as+t2%2C+B.qid+as+q2%0D%0AFROM+qids+A%2C+qids+B%0D%0AWHERE+A.title+%3D+B.title%0D%0Aand+A.id+%21%3D+B.id%0D%0A%3B'>Duplicte qids.</a></li>
                <li><a href='" . $sql_php . "code=SELECT%20%0AA.id%20as%20id1%2C%20A.title%20as%20t1%2C%20A.qid%20as%20q1%2C%20%0AB.id%20as%20id2%2C%20B.title%20as%20t2%2C%20B.qid%20as%20q2%0AFROM%20qids%20A%2C%20qids%20B%0AWHERE%20A.qid%20%3D%20B.qid%0Aand%20A.title%20!%3D%20B.title%0Aand%20A.id%20!%3D%20B.id%0Aand%20B.qid%20!%3D%20%27%27%0A%3B'>Duplicte qids2.</a></li>
            </ul>
        </div>
    </div>
    <form action='sql.php' method='POST'>
        <div class='row'>
            <div class='col-md'>
                <textarea cols='120' rows='7' name='code'>$quaa</textarea>
            </div>
            <div class='col-md'>
                <div class='input-group mb-2'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text'>
                            <label class='mr-sm-2' for='pass'>code:</label>
                        </span>
                    </div>
                    <input class='form-control' type='text' id='pass' name='pass' value='$pass'/>
                </div>
                <div class='input-group mb-3'>
                    <div class='custom-control custom-checkbox custom-control-inline'>
                        <input type='checkbox' class='custom-control-input' id='test' name='test' value='1'>
                        <label class='custom-control-label' for='test'>test</label>
                    </div>
                </div>
                <div class='input-group mb-3'>
                    <div class='custom-control custom-checkbox custom-control-inline'>
                        <input type='checkbox' class='custom-control-input' id='raw' name='raw' value='m'>
                        <label class='custom-control-label' for='raw'>raw</label>
                    </div>
                </div>
                <div class='form-group'>
                    <div class='aligncenter'>
                        <input class='btn btn-primary' type='submit' name='start' value='Start' />
                    </div>
                </div>
        
            </div>
        </div>
    </form>
    ";
};
//---
if ( $qua != '' and ($pass == $sqlpass or $_SERVER['SERVER_NAME'] == 'localhost') ) {
    //---
    if ($_SERVER['SERVER_NAME'] == 'mdwiki.toolforge.org') {
        $uu = quary($qua);
    } else {
        $uu = sqlquary_localhost($qua);
    };
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
        // if ($test != '') { print_r($uu);};
        if ($test != '') { print(var_export($uu)) ;};
        //---
        if ($text == '') {
            if ($test != '') {
                print_r($uu);
            } else {
                print(var_dump($uu));
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
                if (preg_match( '/^\d+$/', $nas, $m ) ) {
                    $ii = '';
                } else {
                    $ff[$nas] = $value;
                };
            };
            //---
            
            $sql_result[$n] = $ff;
        };
        print(json_encode($sql_result));
		//---
		if ( $raw == '66' ) {
			echo '<script>window.close();</script>';
			
		};
		//---
        
    };
    //---
};
//---
//---

/*
$sql = <<<____SQL
     CREATE TABLE IF NOT EXISTS `ticket_hist` (
       `tid` int(11) NOT NULL,
       `trqform` varchar(40) NOT NULL,
       `trsform` varchar(40) NOT NULL,
       `tgen` datetime NOT NULL,
       `tterm` datetime,
       `tstatus` tinyint(1) NOT NULL
     ) ENGINE=ARCHIVE COMMENT='ticket archive';
____SQL;
$result = $this->db->getConnection()->exec($sql);

*/
//---
//---
if ( $raw == '' ) {
    print "</div>";
    //---
    require('foter.php');
    //---
};
//---
?>