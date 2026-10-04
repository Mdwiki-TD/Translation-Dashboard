<?php

namespace App\Utils;

class HtmlUrls
{
    public static function make_mdwiki_href($title)
    {
        if (empty($title)) return $title;

        return "https://mdwiki.org/wiki/" . rawurlencode(str_replace(' ', '_', $title));
    }

    /**
     * Generate a link to an MDWiki user page
     *
     * @param string $user Username
     *
     * @return string HTML anchor element or original username if empty
     */
    public static function make_mdwiki_user_url(string $user): string
    {
        if (!empty($user)) {
            $encodedUser = rawurlencode(str_replace(' ', '_', $user));
            $escapedUser = htmlspecialchars($user, ENT_QUOTES, 'UTF-8');
            return "<a href='https://mdwiki.org/wiki/User:{$encodedUser}' taget='_blank'>{$escapedUser}</a>";
        }
        return $user;
    }

    public static function make_mdwiki_article_url_blank($title, $name = null)
    {
        if (empty($title)) return $title;

        $displayName = $name ? $name : $title;

        $encoded_title = rawurlencode(str_replace(' ', '_', $title));

        return "<a target='_blank' href='https://mdwiki.org/wiki/$encoded_title'>$displayName</a>";
    }

    public static function make_mdwiki_cat_url($category, $name = null)
    {
        if (empty($category)) return $category;

        $new_cat = str_replace('Category:', '', $category);

        $displayName = $name ? $name : $new_cat;

        $encoded_category = rawurlencode(str_replace(' ', '_', $new_cat));

        return "<a target='_blank' href='https://mdwiki.org/wiki/Category:$encoded_category'>$displayName</a>";
    }

    public static function make_wikipedia_url_blank($target, $lang, $name = '', $deleted = false)
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

    public static function make_wikidata_url_blank($qid, $name = '', $default = '')
    {
        if (empty($qid)) return $default;

        $displayName = (!empty($name)) ? $name : $qid;

        $url = "https://wikidata.org/wiki/" . rawurlencode(str_replace(' ', '_', $qid));

        return "<a class='inline' target='_blank' href='$url'>$displayName</a>";
    }
}
