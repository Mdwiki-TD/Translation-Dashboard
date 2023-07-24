<?php

require('header.php');
require('tables.php');
include_once('functions.php');
$dir = 'I:/mdwiki';
    
if (strpos(__file__, '/mnt/') === 0) {
    $dir = '/mnt/nfs/labstore-secondary-tools-project/mdwiki';
}
if (strpos(__file__, '/data/') === 0) {
    $dir = '/data/project/mdwiki';
}
$coden = strtolower($_REQUEST['code']);
$title_o = $_REQUEST['title'];

$useree  = (global_username != '') ? global_username : $_REQUEST['username'];

$tit_line = make_input_group( 'title', 'title', $title_o, 'required');
$cod_line = make_input_group( 'code', 'code', $coden, 'required');

$nana = <<<HTML
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
    HTML;

if (isset($_GET['form'])) echo $nana;

function start_trans_py($title, $test, $fixref, $tra_type) {
    global $dir;
    $title2 = str_replace(' ', '_', $title);
    //---
    $title2 = rawurlencode($title2);
    // $title2 = addslashes($title2);
    //---
    $dd = "python3 $dir/TDpynew/translate.py -title:$title2";
    if ($fixref !== '') {
        $dd .= ' fixref';
    }
    
    if ($tra_type === 'all') {
        $dd .= ' wholearticle';
    }
    
    if ($test !== '') echo "$dd<br>";
    
    $command = escapeshellcmd($dd);
    $output = shell_exec($command);
    
    return $output;
}

function insertPage($title_o, $word, $tr_type, $cat, $coden, $useree, $test) {

    $useree  = escape_string($useree);
    $cat     = escape_string($cat);
    $title_o = escape_string($title_o);
    
    $quae_new = <<<SQL
        INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
        SELECT '$title_o', '$word', '$tr_type', '$cat', '$coden', now(), '$useree', '', '', now()
        WHERE NOT EXISTS
            (SELECT 1
            FROM pages 
                    WHERE title = '$title_o'
                    AND lang = '$coden'
                    AND user = '$useree'
            )
    SQL;

    if ($test != '') echo "<br>$quae_new<br>";

    execute_query($quae_new);
}

if ($title_o != '' && $coden != '' && $useree != '' ) {
    
    $title_o = trim($title_o);
    $coden   = trim($coden);
    $useree  = trim($useree);
    
    $test    = $_REQUEST['test'] ?? '';
    $cat     = $_REQUEST['cat'] ?? '';
    $fixref  = $_REQUEST['fixref'] ?? '';
    $tr_type = $_REQUEST['type'] ?? 'lead';
    
    $useree  = rawurldecode($useree);
    $cat     = rawurldecode($cat);
    $title_o = rawurldecode($title_o);
    
    $word = $Words_table[$title_o] ?? 0; 
    
    if ($tr_type == 'all') { 
        $word = $All_Words_table[$title_o] ?? 0;
    };

    insertPage($title_o, $word, $tr_type, $cat, $coden, $useree, $test);

    $output = start_trans_py($title_o,$test,$fixref,$tr_type);
    
    if (trim($output) == 'true' || isset($_REQUEST['go'])) {
        $url = make_translation_url($title_o, $coden);
        
        $title_o2 = rawurlencode(str_replace ( ' ' , '_' , $title_o ) );
    
        if ($coden == 'en') $url = "//en.wikipedia.org/w/index.php?title=User:Mr._Ibrahem/$title_o2&action=edit";
        
        if ($test != "" && (!isset($_REQUEST['go']))) {
            echo <<<HTML
                $nana
                <br>trim($output) == true<br>
                start_trans_py<br>
                $url
            HTML;
            
        } else {
            echo <<<HTML
                <script type='text/javascript'>
                window.open('$url', '_self');
                </script>
                <noscript>
                    <meta http-equiv='refresh' content='0; url=$url'>
                </noscript>
            HTML;
        };
    
    } elseif (trim($output) == 'notext') {
        $li = make_mdwiki_title($title_o);
        echo <<<HTML
            $nana
            page: $li has no text..<br>
        HTML;
    } else {
        echo <<<HTML
            $nana
            error..<br>($output)
        HTML;
    }
};

echo '</div>';


require('foter.php');
    

?>
