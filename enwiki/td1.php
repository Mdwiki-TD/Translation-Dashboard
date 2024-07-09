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
    private $do_fix_refs;
    private $wholeArticle;

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
            // ---
            if ($first != '') {
                $first .= "\n==References==\n<references />";
            }
        }

        $text = $first;

        return array("text" => $text, "allText" => $allText);
    }

    private function getTextFromMdWiki_raw()
    {
        $first = '';
        $allText = '';

        $url = "https://mdwiki.org/wiki/" . $this->title . "?action=raw";

        test_print("file_get_contents($url);");
        $allText = file_get_contents($url, false, stream_context_create(array('http' => array('follow_location' => false))));

        if ($allText === FALSE) {
            $allText = get_url_result($url);
        };

        if ($this->wholeArticle) {
            $first = $allText;
        } else {
            // split before the first header ==
            $first = explode('==', $allText)[0];
            if ($first != '') {
                $first .= "\n==References==\n<references />";
            }
        }

        $text = $first;

        return array("text" => $text, "allText" => $allText);
    }

    public function parseText()
    {
        $txt = $this->getTextFromMdWiki();
        $text = $txt["text"];
        $allText = $txt["allText"];

        if ($text === '') {
            echo ('no text');
            return "notext";
        }

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
        if ($newText === '') {
            echo ('no text');
            return "notext";
        }
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

        if ($newText === '' || $newText == '' || $newText == 'n' || $newText == 'notext') {
            return "notext";
        }

        if (strlen($newText) < 1000) {
            return 'notext';
        }

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
