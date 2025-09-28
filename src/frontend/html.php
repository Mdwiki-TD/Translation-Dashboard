<?php

namespace TD\Render\Html;
/*
https://live.datatables.net/lopevege/1/edit

Usage:
use function TD\Render\Html\banner_alert;
use function TD\Render\Html\login_card;
use function TD\Render\Html\makeCard;
use function TD\Render\Html\makeColSm4;
use function TD\Render\Html\makeDropdown;
use function TD\Render\Html\make_mdwiki_cat_url;
use function TD\Render\Html\make_col_sm_body;
use function TD\Render\Html\make_datalist_options;
use function TD\Render\Html\make_drop;
use function TD\Render\Html\make_form_check_input;
use function TD\Render\Html\make_input_group;
use function TD\Render\Html\make_input_group_no_col;
use function TD\Render\Html\make_mdwiki_article_url;
use function TD\Render\Html\make_mdwiki_user_url;
use function TD\Render\Html\make_modal_fade;
use function TD\Render\Html\make_project_to_user;
use function TD\Render\Html\make_talk_url;
use function TD\Render\Html\make_target_url;
use function TD\Render\Html\make_translation_url;
*/


function banner_alert($text)
{
    return <<<HTML
	<div class='container'>
		<div class="alert alert-danger" role="alert">
			<i class="bi bi-exclamation-triangle"></i> $text
		</div>
	</div>
	HTML;
}
function login_card()
{
    return <<<HTML
    <div class='card' style='font-weight: bold;'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-md-10'>
                    <a role='button' class='btn btn-outline-primary' onclick='login()'>
                        <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    HTML;
}

function make_modal_fade($label, $text, $id, $button = '')
{
    $exampleModalLabel = rand(1000, 9999);
    return <<<HTML

        <!-- Logout Modal-->
        <div class="modal fade" id="$id" tabindex="-1" role="dialog" aria-labelledby="$exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="$exampleModalLabel">$label</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">$text</div>
                    <div class="modal-footer">
                        $button
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    HTML;
}

function makeDropdown($tab, $cat, $id, $add)
{
    //---
    $options = "";
    //---
    foreach ($tab as $dd) {
        $se = ($cat == $dd) ? 'selected' : '';
        //---
        $options .= <<<HTML
            <option value='$dd' $se>$dd</option>
        HTML;
    };
    //---
    $sel_line = "";
    //---
    if (!empty($add)) {
        $add2 = ($add == 'all') ? 'All' : $add;
        $sel = "";
        if ($cat == $add) $sel = "selected";
        $sel_line = "<option value='$add' $sel>$add2</option>";
    }
    //---
    return <<<HTML
        <select dir="ltr" id="$id" name="$id" class="form-select" data-bs-theme="auto">
            $sel_line
            $options
        </select>
    HTML;
};

function makeColSm4($title, $table, $numb = 4, $table2 = '', $title2 = '')
{
    return <<<HTML
        <div class="col-lg-$numb col-md-6 col-sm-12">
            <div class="card card2 mb-3">
                <div class="card-header">
                    <span class="card-title" style="font-weight:bold;">
                        $title
                    </span>
                    <div style='float: right'>
                        $title2
                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card2">
                    $table
                </div>
                <!-- <div class="card-footer"></div> -->
            </div>
            $table2
        </div>
    HTML;
}
function makeCol($title, $table, $table2)
{
    return <<<HTML
        <div class="col-lg-3 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-lg-12 col-md-6">
                    <div class="card card2 mb-3">
                        <div class="card-header">
                            <span class="card-title" style="font-weight:bold;">
                                $title
                            </span>
                            <div class="card-tools">
                                <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body1 card2">
                            $table
                        </div>
                        <!-- <div class="card-footer"></div> -->
                    </div>
                </div>
                <div class="col-lg-12 col-md-6">
                    $table2
                </div>
            </div>
        </div>
    HTML;
}

function make_drop($uxutable, $code)
{
    $options  =  "";
    //---
    foreach ($uxutable as $name => $cod) {
        $cdcdc = $code == $cod ? "selected" : "";
        $options .= <<<HTML
		<option value='$cod' $cdcdc>$name</option>

		HTML;
    };
    //---
    return $options;
};
//---
function make_mdwiki_article_url($title, $name = null)
{
    if (empty($title)) return $title;
    // ---
    $display_name = $name ? $name : $title;
    // ---
    $encoded_title = rawurlencode(str_replace(' ', '_', $title));
    // ---
    return "<a target='_blank' href='https://mdwiki.org/wiki/$encoded_title'>$display_name</a>";
}

function make_mdwiki_cat_url($category, $name = null)
{
    if (empty($category)) return $category;
    // ---
    $new_cat = str_replace('Category:', '', $category);
    // ---
    $display_name = $name ? $name : $new_cat;
    // ---
    $encoded_category = rawurlencode(str_replace(' ', '_', $new_cat));
    // ---
    return "<a target='_blank' href='https://mdwiki.org/wiki/Category:$encoded_category'>$display_name</a>";
}
//---

function make_translation_url($title, $lang, $tr_type)
{
    //---
    $page = $tr_type == 'all' ? "User:Mr. Ibrahem/$title/full" : "User:Mr. Ibrahem/$title";
    //---
    $params = array(
        'page' => $page,
        'from' => "simple",
        'sx' => 'true',
        'to' => $lang,
        'targettitle' => $title
    );
    //---
    $url = "//$lang.wikipedia.org/wiki/Special:ContentTranslation";
    //---
    // $url .= "?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986) . "#/sx/sentence-selector";
    $url .= "?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986) . "#/sx?previousRoute=dashboard&eventSource=direct_preselect";
    //---
    // $url = "//$lang.wikipedia.org/wiki/Special:ContentTranslation?page=User%3AMr.+Ibrahem%2F$title&from=en&to=$lang&targettitle=$title#draft";
    //---
    return $url;
}

function make_mdwiki_user_url($user)
{
    if (!empty($user)) {
        $encoded_user = rawurlencode(str_replace(' ', '_', $user));
        return "<a href='https://mdwiki.org/wiki/User:$encoded_user' taget='_blank'>$user</a>";
    }
    return $user;
}

function make_target_url($target, $lang, $name = '', $deleted = false)
{
    $display_name = (!empty($name)) ? $name : $target;
    if (!empty($target)) {
        $encoded_target = rawurlencode(str_replace(' ', '_', $target));
        $link = "<a target='_blank' href='https://$lang.wikipedia.org/wiki/$encoded_target'>$display_name</a>";

        if ($deleted == 1) {
            $link .= ' <span class="text-danger">(DELETED)</span>';
        }
        return $link;
    }
    return $target;
}
