<?PHP

namespace Results\ResultsTable\Rows;

/*
Usage:

use function Results\ResultsTable\Rows\make_translate_urls;

*/

use function Results\TrLink\make_tr_link_medwiki;

function make_translate_urls($title, $tra_type, $words, $cod, $cat, $camp, $inprocess, $mdwiki_url, $tra_btn, $_user_, $full_tr_user, $is_video)
{
    $full_translate_url = make_tr_link_medwiki($title, $cod, $cat, $camp, "all", $words);
    $translate_url = make_tr_link_medwiki($title, $cod, $cat, $camp, $tra_type, $words);
    //---
    $tab = "<a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Translate</a>";
    //---
    if ($full_tr_user && !$is_video) {
        $tab = <<<HTML
            <div class='inline'>
                <a href='$translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Lead</a>
                <a href='$full_translate_url' class='btn btn-outline-primary btn-sm' target='_blank'>Full</a>
            </div>
            HTML;
    }
    //---
    if ($GLOBALS['global_username'] == '') {
        $tab = <<<HTML
            <a role='button' class='btn btn-outline-primary' onclick='login()'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>
            HTML;
        //---
        $translate_url = $mdwiki_url;
    }
    //---
    if ($inprocess) {
        if ($tra_btn != '1' && $_user_ != $GLOBALS['global_username']) {
            $tab = '';
            $translate_url = $mdwiki_url;
            $full_translate_url = $mdwiki_url;
        };
    };
    // ---
    return [$tab, $translate_url, $full_translate_url];
}
