<?php
$req  = $_REQUEST;
$post = $_POST;
$get  = $_GET;
// ---
require 'header.php';
require 'tables.php';
include_once 'functions.php';
include_once 'enwiki/td1.php';
// ---
$pathParts = explode('public_html', __FILE__);
// the root path is the first part of the split file path
$ROOT_PATH = $pathParts[0];
// echo "ROOT_PATH:$ROOT_PATH<br>";
// ---
$coden = strtolower($_GET['code']);
$title_o = $_GET['title'];
// $useree  = (global_username != '') ? global_username : $_GET['username'];
$useree  = (global_username != '') ? global_username : '';
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
function insertPage($title_o, $word, $tr_type, $cat, $coden, $useree, $test) {
    $useree  = escape_string($useree);
    $cat     = escape_string($cat);
    $title_o = escape_string($title_o);
    
    $quae_new = <<<SQL
        INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
        SELECT ?, ?, ?, ?, ?, now(), ?, '', '', now()
        WHERE NOT EXISTS
            (SELECT 1
            FROM pages 
                    WHERE title = ?
                    AND lang = ?
                    AND user = ?
            )
    SQL;
    $params = [$title_o, $word, $tr_type, $cat, $coden, $useree, $title_o, $coden, $useree];
    if ($test != '') echo "<br>$quae_new<br>";
    execute_query($quae_new, $params=$params);
}
if ($useree == '' ) {
    echo <<<HTML
    <div class='card' style='font-weight: bold;'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-md-10'>
                    <a role='button' class='btn btn-primary' onclick='login()'>
                        <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    HTML;
}
if ($title_o != '' && $coden != '' && $useree != '' ) {
    $title_o = trim($title_o);
    $coden   = trim($coden);
    $useree  = trim($useree);
    
    $test    = $_GET['test'] ?? '';
    $cat     = $_GET['cat'] ?? '';
    $fixref  = $_GET['fixref'] ?? '';
    $tr_type = $_GET['type'] ?? 'lead';
    
    $useree  = rawurldecode($useree);
    $cat     = rawurldecode($cat);
    $title_o = rawurldecode($title_o);
    
    $word = $Words_table[$title_o] ?? 0; 
    
    if ($tr_type == 'all') { 
        $word = $All_Words_table[$title_o] ?? 0;
    };
    insertPage($title_o, $word, $tr_type, $cat, $coden, $useree, $test);
    $output = startTranslatePhp($title_o, $tr_type);
    
    if (trim($output) == 'true' || isset($_GET['go'])) {
        $url = make_translation_url($title_o, $coden, $tr_type);
        
        $title_o2 = rawurlencode(str_replace ( ' ' , '_' , $title_o ) );
    
        if ($coden == 'en') {
            $page = $tr_type == 'all' ? "User:Mr. Ibrahem/$title_o2/full" : "User:Mr. Ibrahem/$title_o2";
            //---
            $url = "//en.wikipedia.org/w/index.php?title=$page&action=edit";
        }
        
        if ($test != "" && (!isset($_GET['go']))) {
            echo <<<HTML
                $nana
                <br>trim($output) == true<br>
                start_tr<br>
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
            save to enwiki: error..<br>($output)
        HTML;
    }
};
echo '</div>';
require 'foter.php';
