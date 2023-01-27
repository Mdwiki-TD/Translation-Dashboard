<?php
//---
require('header.php');
require('tables.php');
include_once('functions.php');
//---
$coden = strtolower($_REQUEST['code']);
$title_o = $_REQUEST['title'];
//---
$tit_line = make_input_group( 'title', 'title', $title_o, 'required');
$cod_line = make_input_group( 'code', 'code', $coden, 'required');
//---
$nana = "
    <div class='card' style='font-weight: bold;'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-md-10 col-md-offset-1'>
                    <form action='translate.php' method='GET'>
                        $tit_line
                        $cod_line
                        <input class='btn btn-primary' type='submit' name='start' value='Start' />
                    </form>
                </div>
            </div>
        </div>
    </div>
    ";
//---
if (isset($_GET['form'])) echo $nana;
//---
function start_trans_py($title, $test, $fixref, $tra_type) {
    //---
    $title2 = $title;
    $title2 = rawurlencode(str_replace ( ' ' , '_' , $title2 ) );
    //---
    $dir = '/mdwiki';
    //---
    if ( strpos( __file__ , '/mnt/' ) === 0 ) $dir = "/mnt/nfs/labstore-secondary-tools-project/mdwiki";
    //---
    $dd = "python3 $dir/TDpy/translate.py -title:$title2" ;
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
    $test    = get_request('test', '');
    $cat     = get_request('cat', '');
    $fixref  = get_request('fixref', '');
    $tr_type = get_request('type', 'lead');
    //---
    $useree  = rawurldecode($useree);
    $cat     = rawurldecode($cat);
    $title_o = rawurldecode($title_o);
    //---
    $title_o2 = $title_o;
    //$title_o2 = ucfirst(trim($title_o2));
    $title_o2 = rawurlencode(str_replace ( ' ' , '_' , $title_o2 ) );
    //---
    $user2  = rawurlencode(str_replace ( ' ' , '_' , $useree ));
    $cat2   = ($cat != '') ? rawurlencode(str_replace ( ' ' , '_' , $cat )) : '';
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
    if ($test != '') echo "<br>$quae<br>";
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
    quary_a($quae_new);
    //---
    $output = start_trans_py($title_o,$test,$fixref,$tr_type);
    // };
    //---
    if (trim($output) == 'true' || isset($_REQUEST['go'])) {
        $title_o2 = rawurlEncode($title_o);
        //---
        $url = "//$coden.wikipedia.org/wiki/Special:ContentTranslation?page=User%3AMr.+Ibrahem%2F$title_o2";
        $url .= "&from=en&to=$coden&targettitle=$title_o2#draft";
        //---
        if ($coden == 'en') $url = "//en.wikipedia.org/w/index.php?title=User:Mr._Ibrahem/$title_o2&action=edit";
        //---
        if ($test != "" && (!isset($_REQUEST['go']))) {
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
