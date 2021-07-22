<?php
//--------------------
require('header1.php');
require('tables.php');
require('functions.php');
require('newtranslate.php');
//--------------------
echo '<div class="col-md-10 col-md-offset-1" align=left >
<span class="btn btn-primary btn-lg btn-block"></span>
';
//--------------------
$coden = strtolower($_REQUEST['code']);
$title_o = $_REQUEST['title'];
//--------------------
echo "<pre>
<form action='translate.php' method='GET'>
<label>title: </label><input class='span2' type='text' value='$title_o' name='title'></input><br>
<label>code : </label><input class='span2' type='text' value='$coden' name='code'><br>
<input class='btn btn-lg' type='submit' name='start' value='Start' />
</form>
</pre>";
//--------------------
//--------------------
if ($title_o != '' and $coden != '') {
    //--------------------
    $useree = $_REQUEST['username'];
    $cat = $_REQUEST['cat'];
    $test = $_REQUEST['test'];
    $fixref = $_REQUEST['fixref'];
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
    $objDateTime = new DateTime('NOW');
    $date = $objDateTime->format('Y-m-d');
    //--------------------
    //--------------------
    $quae = "
INSERT INTO pages (title, word, cat, lang, date, user, target, pupdate)
VALUES ('$title_o', '$word', '$cat', '$coden', '$date', '$useree', '', '')
";
    //--------------------
    $quae_new = "
INSERT INTO pages (title, word, cat, lang, date, user, target, pupdate)
    SELECT '$title_o', '$word', '$cat', '$coden', '$date', '$useree', '', ''
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
    $output = start_trans_py($title_o,$test,$fixref);
    //--------------------
    if (trim($output) == 'true' ) {
        print "<script type='text/javascript'>";
        $title_o2 = rawurlEncode($title_o);
        //--------------------
        $url = "//$coden.wikipedia.org/wiki/Special:ContentTranslation?page=User%3AMr._Ibrahem%2F$title_o2&from=en&to=$coden&targettitle=$title_o2";
        //--------------------
        if ($test != "") {
            print $url;
        } else {
            print "window.open('$url', '_self');";
        };
        //--------------------
        print "</script>";
    } else {
        print 'error..<br>';
        print $output;
    }
    }
//--------------------
require('foter1.php');
print '</div>';
    //--------------------
//--------------------
?>
