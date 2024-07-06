<?php

include_once __DIR__ . '/../actions/mdwiki_api.php';
include_once __DIR__ . '/../Tables/langcode.php';
include_once 'en_api.php';
include_once 'fixtext.php';
include_once 'fixref.php';

class WikiTranslator
{
    private $title;
    private $traType;

    public function __construct($title, $traType, $do_fix_refs=true)
    {
        $this->title = $title;
        $this->title = str_replace(' ', '_', $this->title);
        $this->traType = $traType;
        $this->do_fix_refs = $do_fix_refs;
        $this->wholeArticle = ($traType == 'all') ? true : false;
    }

    private function getTextFromMdWiki()
    {
        $first = '';
        $params2 = array(
            "action" => "parse",
            "format" => "json",
            "page" => $this->title,
            "prop" => "wikitext"
        );
        $json2 = get_mdwiki_url_with_params($params2);

        $allText = $json2["parse"]["wikitext"]["*"] ?? '';

        if ($this->wholeArticle) {
            $first = $allText;
        } else {
            $params = array(
                "action" => "parse",
                "format" => "json",
                "page" => $this->title,
                "section" => "0",
                "prop" => "wikitext"
            );
            $json1 = get_mdwiki_url_with_params($params);
            $first = $json1["parse"]["wikitext"]["*"] ?? '';
        }

        $text = $first;

        if ($text === '') {
            return "notext";
        }

        if (!$this->wholeArticle) {
            $text .= "\n==References==\n<references />";
        }

        return [$text, $allText];
    }

    public function parseText()
    {
        $txt = $this->getTextFromMdWiki();
        $text = $txt[0];
        $allText = $txt[1];
        $newText = $text;

        if ($this->do_fix_refs) {
            $newText = fix_ref($newText, $allText);
        };

        $newText = text_changes_work($newText);

        $newText = str_replace('[[Category:', '[[:Category:', $newText);

        if ($newText === '') {
            echo ('no text');
            return "";
        }

        return $newText;
    }

    private function PostToEnwiki($newText)
    {
        $suus = 'from https://mdwiki.org/wiki/' . str_replace(' ', '_', $this->title);
        $title2 = 'User:Mr. Ibrahem/' . $this->title;

        if ($this->wholeArticle) {
            $title2 = 'User:Mr. Ibrahem/' . $this->title . '/full';
        }

        $result = do_edit($title2, $newText, $suus);
        $success = $result['edit']['result'] ?? '';

        if ($success == 'Success') {
            return 'true';
        }

        return $success;
    }
    public function startTranslate()
    {
        /*
        1. get text from mdwiki.org
        2. fix ref
        3. fix text
        4. put to enwiki
        5. return result
        */
        $newText = $this->parseText();

        $success = $this->PostToEnwiki($newText);

        return $success;
    }
}

function startTranslatePhp($title, $traType, $return_text, $do_fix_refs=true)
{

    $wikiTranslator = new WikiTranslator($title, $traType, $do_fix_refs=$do_fix_refs);

    if ($return_text) {
        return $wikiTranslator->parseText();
    }

    $result = $wikiTranslator->startTranslate();

    return $result;
}
