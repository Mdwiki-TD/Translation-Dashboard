<?php

namespace App\Utils\Html;

/**
 * Generate a Bootstrap modal dialog
 *
 * @param string $label   Modal title
 * @param string $text    Modal body content
 * @param string $id      Modal element ID
 * @param string $button  Additional button HTML (optional)
 *
 * @return string HTML for the modal
 */
function make_modal_fade(string $label, string $text, string $id, string $button = ''): string
{
    $randomId = 'modalLabel' . random_int(1000, 9999);

    return <<<HTML

        <!-- Logout Modal-->
        <div class="modal fade" id="{$id}" tabindex="-1" role="dialog" aria-labelledby="{$randomId}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="{$randomId}">{$label}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">{$text}</div>
                    <div class="modal-footer">
                        {$button}
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    HTML;
}

/**
 * Generate a dropdown select element
 *
 * @param array<int,string> $tab  Options array
 * @param string            $cat  Currently selected value
 * @param string            $id   Select element ID and name
 * @param string            $add  Additional option (e.g., 'all')
 *
 * @return string HTML for the select element
 */
function makeDropdown(array $tab, string $cat, string $id, string $add): string
{
    $options = "";

    foreach ($tab as $dd) {
        $se = ($cat === $dd) ? 'selected' : '';

        if (empty($dd)) continue;

        $escaped = htmlspecialchars($dd, ENT_QUOTES, 'UTF-8');
        $options .= "<option value='{$escaped}' {$se}>{$escaped}</option>";
    }

    $selLine = "";
    if (!empty($add)) {
        $add2 = ($add === 'all') ? 'All' : $add;
        $sel = ($cat === $add) ? "selected" : "";
        $escapedAdd = htmlspecialchars($add, ENT_QUOTES, 'UTF-8');
        $escapedAdd2 = htmlspecialchars($add2, ENT_QUOTES, 'UTF-8');
        $selLine = "<option value='{$escapedAdd}' {$sel}>{$escapedAdd2}</option>";
    }

    return <<<HTML
        <select dir="ltr" id="{$id}" name="{$id}" class="form-select" data-bs-theme="auto">
            {$selLine}
            {$options}
        </select>
    HTML;
}

/**
 * Generate a link to an MDWiki user page
 *
 * @param string $user Username
 *
 * @return string HTML anchor element or original username if empty
 */
function make_mdwiki_user_url(string $user): string
{
    if (!empty($user)) {
        $encodedUser = rawurlencode(str_replace(' ', '_', $user));
        $escapedUser = htmlspecialchars($user, ENT_QUOTES, 'UTF-8');
        return "<a href='https://mdwiki.org/wiki/User:{$encodedUser}' taget='_blank'>{$escapedUser}</a>";
    }
    return $user;
}

function makeColSm4(string $title, string $table, int $numb = 4, string $table2 = '', string $title2 = ''): string
{
    $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    return <<<HTML
        <div class="col-lg-$numb col-md-6 col-sm-12">
            <div class="card card2 mb-3">
                <div class="card-header">
                    <span class="card-title" style="font-weight:bold;">
                        {$escapedTitle}
                    </span>
                    <div style='float: right'>
                        {$title2}
                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card2">
                    {$table}
                </div>
                <!-- <div class="card-footer"></div> -->
            </div>
            {$table2}
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


function make_mdwiki_href($title)
{
    if (empty($title)) return $title;

    return "https://mdwiki.org/wiki/" . rawurlencode(str_replace(' ', '_', $title));
}

function make_mdwiki_article_url_blank($title, $name = null)
{
    if (empty($title)) return $title;

    $displayName = $name ? $name : $title;

    $encoded_title = rawurlencode(str_replace(' ', '_', $title));

    return "<a target='_blank' href='https://mdwiki.org/wiki/$encoded_title'>$displayName</a>";
}

function make_mdwiki_cat_url($category, $name = null)
{
    if (empty($category)) return $category;

    $new_cat = str_replace('Category:', '', $category);

    $displayName = $name ? $name : $new_cat;

    $encoded_category = rawurlencode(str_replace(' ', '_', $new_cat));

    return "<a target='_blank' href='https://mdwiki.org/wiki/Category:$encoded_category'>$displayName</a>";
}

function make_wikipedia_url_blank($target, $lang, $name = '', $deleted = false)
{

    if (empty($target)) return $target;

    $displayName = (!empty($name)) ? $name : $target;

    $encodedTarget = rawurlencode(str_replace(' ', '_', $target));

    $link = "<a target='_blank' href='https://$lang.wikipedia.org/wiki/$encodedTarget'>$displayName</a>";

    if ($deleted == 1) {
        $link .= ' <span class="text-danger">(DELETED)</span>';
    }

    return $link;
}

function make_wikidata_url_blank($qid, $name = '', $default = '')
{
    if (empty($qid)) return $default;

    $displayName = (!empty($name)) ? $name : $qid;

    $url = "https://wikidata.org/wiki/" . rawurlencode(str_replace(' ', '_', $qid));

    return "<a class='inline' target='_blank' href='$url'>$displayName</a>";
}
