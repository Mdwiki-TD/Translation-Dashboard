<?php
//--------------------
require('header.php');
require('tables.php');
include_once('functions.php');
//--------------------
$nana = '<div class="col-md-10 col-md-offset-1" align=left >
<span class="btn btn-primary btn-lg btn-block"></span>
';
//--------------------
$coden = strtolower($_REQUEST['code']);
$title_o = $_REQUEST['title'];
//--------------------
$nana .= "<div class='ppre'>
<form action='translate.php' method='GET'>
<label>title: </label><input class='span2' type='text' value='$title_o' name='title'></input><br>
<label>code : </label><input class='span2' type='text' value='$coden' name='code'><br>
<input class='btn btn-lg' type='submit' name='start' value='Start' />
</form>
</div>";
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
if ($title_o != '' and $coden != '') {
    //--------------------
    $useree = $_REQUEST['username'];
    $cat = $_REQUEST['cat'];
    $test = $_REQUEST['test'];
    $fixref = $_REQUEST['fixref'];
    $Translat_type  = $_REQUEST['type'];
    //--------------------
    $useree = rawurldecode($useree);
    $cat = rawurldecode($cat);
    $title_o = rawurldecode($title_o);
    //--------------------
    $title_o2 = $title_o;
    //$title_o2 = ucfirst(trim($title_o2));
    $title_o2 = rawurlencode(str_replace ( ' ' , '_' , $title_o2 ) );
    //--------------------
    $user2  = rawurlencode(str_replace ( ' ' , '_' , $useree ));
    $cat2   = rawurlencode(str_replace ( ' ' , '_' , $cat ));
    //==========================
    $word = $Words_table->{$title_o}; 
    //--------------------
    if ($Translat_type == 'all') { $word = $All_Words_table->{$title_o};  };
    //--------------------
    $objDateTime = new DateTime('NOW');
    $date = $objDateTime->format('Y-m-d');
    //--------------------
    //--------------------
    $quae = "
INSERT INTO pages (title, word, translate_type, cat, lang, date, user, target, pupdate)
VALUES ('$title_o', '$word', '$Translat_type', '$cat', '$coden', '$date', '$useree', '', '')
";
    //--------------------
    $quae_new = "
INSERT INTO pages (title, word, translate_type, cat, lang, date, user, target, pupdate)
    SELECT '$title_o', '$word', '$Translat_type', '$cat', '$coden', '$date', '$useree', '', ''
    WHERE NOT EXISTS
        (SELECT 1
         FROM pages 
                   WHERE title = '$title_o'
                   AND lang = '$coden'
                   AND user = '$useree'
        )
";
    //--------------------
    quary($quae_new);
    //--------------------
    //==========================
    $output = start_trans_py($title_o,$test,$fixref,$Translat_type);
    //--------------------
    if (trim($output) == 'true' ) {
        $title_o2 = rawurlEncode($title_o);
        //--------------------
        $url = "//$coden.wikipedia.org/wiki/Special:ContentTranslation?page=User%3AMr.+Ibrahem%2F$title_o2";
        $url .= "&from=en&to=$coden&targettitle=$title_o2#draft";
        //--------------------
        if ($test != "") {
            //--------------------
            print $nana;
            print '<br>trim($output) == true<br>';
            print 'start_trans_py<br>';
            print $url;
            //--------------------
        } else {
            // =======================
            header( "Location: " . $url );
            exit;
            // =======================
            $zaza = "
    <script type='text/javascript'>
    window.open('$url', '_self');
</script>";
            // =======================
            print $zaza;
        };
    //--------------------
    } else {
        print $nana;
        print 'error..<br>';
        print $output;
    }
};
//--------------------
require('foter.php');
print '</div>';
    //--------------------
//--------------------
?>
