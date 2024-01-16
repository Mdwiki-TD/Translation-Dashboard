<?php
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
/*
//---

UPDATE pages

SET title = REPLACE(title, char(9), ''),
target = REPLACE(target, char(9), '')
//---
delete from pages where target = '' and date < ADDDATE(CURDATE(), INTERVAL -7 DAY)
select * from pages where target = '' and date < ADDDATE(CURDATE(), INTERVAL -7 DAY)
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
include_once 'td_config.php';
$ini = Read_ini_file('OAuthConfig.ini');
include_once 'functions.php';
//---
$sqlpass = $ini['sqlpass'];
//---
$pass = $_REQUEST['pass'] ?? '';
$qua  = $_REQUEST['code'] ?? '';
$raw  = $_REQUEST['raw'] ?? '';
//---
if ( $raw == '' ) {
    require 'header.php';
    //---
	if (!in_array(global_username, $usrs)) {
		echo "<meta http-equiv='refresh' content='0; url=index.php'>";
		exit;
	};
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
	$sql_php = "sql.php?pass=$pass";
    //---
    $qu1 = urlencode("SELECT 
        A.id as id1, A.target as t1,
        B.id as id2, B.target as t2
        FROM views A, views B
        WHERE A.target = B.target
        and A.lang = B.lang
        and A.id != B.id
        ;");
    //---
    $qu2 = urlencode("SELECT * from pages p1 
        where p1.target = '' and EXISTS  (SELECT 1 FROM pages p2 WHERE p1.title = p2.title and p2.target != ''
        and p1.lang = p2.lang
        )");
    //---
    $qu3 = urlencode("SELECT A.lang as lang,A.title as title, 
        A.id AS id1, A.user AS u1, A.target as T1, A.date as d1,
        B.id AS id2, B.user AS u2, B.target as T2, B.date as d2
        FROM pages A, pages B
        WHERE A.id <> B.id
        AND A.title = B.title
        AND A.lang = B.lang
        and A.target != ''
        ORDER BY A.title;");
    //---
    $qu4 = urlencode("SELECT 
        A.id as id1, A.title as t1, A.qid as q1, 
        B.id as id2, B.title as t2, B.qid as q2
        FROM qids A, qids B
        WHERE A.title = B.title
        and A.id != B.id
        ;");
    //---
    $qu5 = urlencode("SELECT 
        A.id as id1, A.title as t1, A.qid as q1, 
        B.id as id2, B.title as t2, B.qid as q2
        FROM qids A, qids B
        WHERE A.qid = B.qid
        and A.title != B.title
        and A.id != B.id
        and B.qid != ''
        ;");
    //---
    $qu6 = urlencode("select * from pages where target = '' and date < ADDDATE(CURDATE(), INTERVAL -7 DAY)");
    //---
    echo <<<HTML
        <div class='row'>
            <div class='col-md'>
                <ul>
                    <li><a href='$sql_php&code=show tables;'>show tables</a></li>
                    <li><a href='$sql_php&code=describe words;'>describe words;</a></li>
                    <li><a href='$sql_php&code=describe pages;'>describe pages;</a></li>
                    </ul>
                </div>
                <div class='col-md'>
                    <ul>
                    <li><a href='$sql_php&code=select * from words;'>select * from words;</a></li>
                    <li><a href='$sql_php&code=select * from pages;'>select * from pages;</a></li>
                    <li><a href='$sql_php&code=select * from qids;'>select * from qids;</a></li>
                    <li><a href='$sql_php&code=$qu1'>Duplicte views.</a></li>
                    </ul>
                </div>
                <div class='col-md'>
                    <ul>
                    <li><a href='$sql_php&code=$qu2'>Duplicte pages to remove.</a></li>
                    <li><a href='$sql_php&code=$qu3'>Duplicte pages2.</a></li>
                    </ul>
                </div>
                <div class='col-md'>
                    <ul>
                    <li><a href='$sql_php&code=$qu4'>Duplicte qids.</a></li>
                    <li><a href='$sql_php&code=$qu5'>Duplicte qids2.</a></li>
                    <li><a href='$sql_php&code=$qu6'>In process > 7.</a></li>
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
                        <span class='input-group-text'>
                            <label class='mr-sm-2' for='pass'>code:</label>
                        </span>
                        <input class='form-control' type='text' name='pass' value='$pass'/>
                    </div>
                    <div class='input-group mb-3'>
                        <div class='custom-control custom-checkbox custom-control-inline'>
                            <input type='checkbox' class='custom-control-input' name='test' value='1'>
                            <label class='custom-control-label' for='test'>test</label>
                        </div>
                    </div>
                    <div class='input-group mb-3'>
                        <div class='custom-control custom-checkbox custom-control-inline'>
                            <input type='checkbox' class='custom-control-input' name='raw' value='m'>
                            <label class='custom-control-label' for='raw'>raw</label>
                        </div>
                    </div>
                    <div class='input-group'>
                        <div class='aligncenter'>
                            <input class='btn btn-primary' type='submit' name='start' value='Start' />
                        </div>
                    </div>
            
                </div>
            </div>
        </form>
    HTML;
};
//---
if ( $qua != '' and ($pass == $sqlpass or $_SERVER['SERVER_NAME'] == 'localhost') ) {
    //---
    require 'sql_result.php';
    make_sql_result( $qua, $raw );
    //---
};
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
if ( $raw == '' ) {
    print "</div>";
    //---
    require 'foter.php';
    //---
};
//---
?>