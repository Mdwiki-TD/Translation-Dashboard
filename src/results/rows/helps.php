<?PHP

namespace Results\ResultsTable\Rows;

/*
Usage:

use function Results\ResultsTable\Rows\make_translate_urls;

*/

use function Results\TrLink\make_tr_link_medwiki;
use function TD\Render\Html\make_mdwiki_href;
use function Results\TrLink\make_ContentTranslation_url;

function make_translate_urls($title, $tra_type, $words, $langcode, $cat, $camp, $inprocess, $tra_btn, $_user_, $full_tr_user, $global_username)
{
    //---
    // if $inprocess and $tra_btn is 1 then show the translate button for
    //---
    // $mdwiki_url = "//mdwiki.org/wiki/" . str_replace('+', '_', rawurlEncode($title));
    $mdwiki_url = make_mdwiki_href($title);
    //---
    if ($global_username == '') {
        //---
        $tab = <<<HTML
            <a role='button' class='btn btn-outline-primary' onclick='login()'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>
            HTML;
        //---
        return [$tab, $mdwiki_url, $mdwiki_url];
    }
    //---
    // if lower $title startswith video
    $tra_type = "lead";
    $is_video = false;
    //---
    if (strtolower(substr($title, 0, 6)) == 'video:') {
        $is_video = true;
        $tra_type = 'all';
    };
    //---
    if ($inprocess) {
        // links directly to ContentTranslation
        $full_translate_url = make_ContentTranslation_url($title, $langcode, $cat, $camp, 'all');
        $translate_url = make_ContentTranslation_url($title, $langcode, $cat, $camp, $tra_type);
    } else {
        // links to translate_med/index.php
        $full_translate_url = make_tr_link_medwiki($title, $langcode, $cat, $camp, "all", $words);
        $translate_url = make_tr_link_medwiki($title, $langcode, $cat, $camp, $tra_type, $words);
    }
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
    if ($inprocess) {
        if ($tra_btn != 1 && $_user_ != $global_username) {
            $tab = '';
            $translate_url = $mdwiki_url;
            $full_translate_url = $mdwiki_url;
        };
    };
    // ---
    return [$tab, $translate_url, $full_translate_url];
}
