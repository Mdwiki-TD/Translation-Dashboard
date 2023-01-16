<?php
//---
require('header.php');
require('tables.php');
include_once('functions.php');
//---
$coden = strtolower($_REQUEST['code']);
$title_o = $_REQUEST['title'];
//---
$nana = "
<div class='col-md-10 col-md-offset-1' align=left >
    <div class='ppre'>
    <form action='translate.php' method='GET'>
        <label>title: </label><input class='span2' type='text' value='$title_o' name='title'></input><br>
        <label>code : </label><input class='span2' type='text' value='$coden' name='code'><br>
        <input class='btn btn-lg' type='submit' name='start' value='Start' />
    </form>
    </div>
    ";
//---
function start_trans_py($title,$test,$fixref,$tra_type) {
    //---
    $title2 = $title;
    $title2 = rawurlencode(str_replace ( ' ' , '_' , $title2 ) );
    //---
    $dd = "python3 /mnt/nfs/labstore-secondary-tools-project/mdwiki/TDpy/translate.py -title:$title2" ;
    if ($fixref != '' ) $dd = $dd . ' fixref';
    //---  
    if ($tra_type == 'all' ) $dd = $dd . ' wholearticle';
    //---  
    if ($test != "") { print $dd . '<br>'; } ; 
    //---  
    $command = escapeshellcmd( $dd );
    $output = shell_exec($command);
    //---  
    return $output;
};
//---
$useree  = $username != '' ? $username : $_REQUEST['username'];
//---
if ($title_o != '' && $coden != '' && $useree != '' ) {
    //---
    $newaa 	 = get_request('newaa');
    $cat 	 = get_request('cat');
    $test 	 = get_request('test');
    $fixref  = get_request('fixref');
    $tr_type = get_request('type');
    //---
    $useree = rawurldecode($useree);
    $cat = rawurldecode($cat);
    $title_o = rawurldecode($title_o);
    //---
    $title_o2 = $title_o;
    //$title_o2 = ucfirst(trim($title_o2));
    $title_o2 = rawurlencode(str_replace ( ' ' , '_' , $title_o2 ) );
    //---
    $user2  = rawurlencode(str_replace ( ' ' , '_' , $useree ));
    $cat2   = rawurlencode(str_replace ( ' ' , '_' , $cat ));
    //---
    $word = isset($Words_table[$title_o]) ? $Words_table[$title_o] : 0; 
    //---
    if ($tr_type == 'all') { 
        $word = isset($All_Words_table[$title_o]) ? $All_Words_table[$title_o] : 0;
    };
    //---
    $objDateTime = new DateTime('NOW');
    $date = $objDateTime->format('Y-m-d');
    //---
    $quae = "
INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target)
VALUES ('$title_o', '$word', '$tr_type', '$cat', '$coden', '$date', '$useree', '', '')
";
    //---
	if (isset($test)) echo "<br>$quae<br>";
    //---
    $quae_new = "
INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target)
    SELECT '$title_o', '$word', '$tr_type', '$cat', '$coden', '$date', '$useree', '', ''
    WHERE NOT EXISTS
        (SELECT 1
         FROM pages 
                   WHERE title = '$title_o'
                   AND lang = '$coden'
                   AND user = '$useree'
        )
";
    //---
    quary($quae_new);
    //---
    //---
    // if ($newaa != '') {
        // $output = start_trans_php($title_o,$test,$fixref,$tr_type);
    // } else {
	$output = start_trans_py($title_o,$test,$fixref,$tr_type);
    // };
    //---
    if (trim($output) == 'true') {
        $title_o2 = rawurlEncode($title_o);
        //---
        $url = "//$coden.wikipedia.org/wiki/Special:ContentTranslation?page=User%3AMr.+Ibrahem%2F$title_o2";
        $url .= "&from=en&to=$coden&targettitle=$title_o2#draft";
        //---
        if ($coden == 'en') $url = "//en.wikipedia.org/w/index.php?title=User:Mr._Ibrahem/$title_o2&action=edit";
        //---
        if ($test != "") {
            //---
            print $nana;
            print '<br>trim($output) == true<br>';
            print 'start_trans_py<br>';
            print $url;
            //---
        } else {
            //---
            // header( "Location: " . $url );
            // exit;
            //---
            $zaza = "
    <script type='text/javascript'>
    window.open('$url', '_self');
</script>
<noscript>
    <meta http-equiv='refresh' content='0; url=$url'>
</noscript>";
            //---
            print $zaza;
        };
    //---
    } else {
        print $nana;
        print 'error..<br>';
        print $output;
    }
};
//---
echo '</div>';

//---
require('foter.php');
    //---
//---
?>
