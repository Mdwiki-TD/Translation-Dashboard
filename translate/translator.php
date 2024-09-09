<?php

namespace Translate\Translator;

/*
Usage:

use function Translate\Translator\startTranslatePhp;
use function Translate\Translator\TranslatePhpEditText;

*/


include_once __DIR__ . '/../actions/mdwiki_api.php';
include_once __DIR__ . '/../Tables/langcode.php';
include_once __DIR__ . '/en_api.php';
include_once __DIR__ . '/fixtext.php';

use function Actions\WikiApi\get_url_result_curl;
use function Actions\Functions\test_print;
use function Translate\EnAPI\do_en_edit;
use function Translate\FixText\text_changes_work;

use function Actions\MdwikiApi\get_mdwiki_url_with_params;

class WikiTranslator
{
    private $title;
    private $traType;
    private $expend_refs;
    private $wholeArticle;
    public $revid;
    public $title2;
    public $url;

    public function __construct($title, $traType, $expend_refs = true)
    {
        $this->title = $title;
        $this->title = str_replace(' ', '_', $this->title);
        $this->traType = $traType;
        $this->expend_refs = $expend_refs;
        $this->wholeArticle = ($traType == 'all') ? true : false;
        $this->revid = "";

        $user = 'User:Mr. Ibrahem';
        // if global_username is MdWikiBot then use it
        if (global_username == 'MdWikiBot') {
            $user = 'User:MdWikiBot';
        }
        $this->title2 = $user . '/' . $this->title;

        if ($this->wholeArticle) {
            $this->title2 = $user . '/' . $this->title . '/full';
        }

        $this->url = "https://simple.wikipedia.org/w/index.php?title={$this->title2}";

        $urle = "<a target='_blank' href='$this->url'>{$this->title2}</a>";

        test_print("title: $urle");
    }

    private function getTextFromMdWiki()
    {
        $first = '';
        $params2 = array(
            "action" => "parse",
            "format" => "json",
            "page" => $this->title,
            "prop" => "wikitext|revid"
        );
        $json2 = get_mdwiki_url_with_params($params2);

        $allText = $json2["parse"]["wikitext"]["*"] ?? '';
        $this->revid = $json2["parse"]["revid"] ?? '';

        if ($this->wholeArticle) {
            $first = $allText;
        } else {
            $params = array(
                "action" => "parse",
                "format" => "json",
                "page" => $this->title,
                "section" => "0",
                "prop" => "wikitext|revid"
            );
            $json1 = get_mdwiki_url_with_params($params);
            $first = $json1["parse"]["wikitext"]["*"] ?? '';
            // ---
            if (!empty($first)) {
                $first .= "\n==References==\n<references />";
            }
        }

        $text = $first;

        return array("text" => $text, "allText" => $allText);
    }

    public function EditTexts($text, $allText)
    {

        $newText = $text;

        $newText = text_changes_work($newText, $allText, $this->expend_refs, $this->title);

        if (empty($newText)) {
            echo ('no text');
            return "";
        }
        // if newtext has Category:Mdwiki Translation Dashboard articles dont add it again!
        if (strpos($newText, 'Category:Mdwiki Translation Dashboard articles') === false) {
            $newText .= "\n\n[[Category:Mdwiki Translation Dashboard articles]]";
        }

        return $newText;
    }

    public function parseText()
    {
        $txt = $this->getTextFromMdWiki();
        $text = $txt["text"] ?? "";
        $allText = $txt["allText"] ?? "";

        if (empty($text)) {
            echo ('no text');
            return "notext";
        }

        $newText = $this->EditTexts($text, $allText);
        return $newText;
    }

    private function PostToEnwiki($newText)
    {
        if (empty($newText)) {
            echo ('no text');
            return "notext";
        }
        // $suus = 'from https://mdwiki.org/wiki/' . str_replace(' ', '_', $this->title);

        $title2 = str_replace('_', ' ', $this->title);

        $suus = 'from mdwiki: [[:mdwiki:Special:Redirect/revision/' . $this->revid . '|' . $title2 . ']]';
        // $suus = '';
        $result = do_en_edit($this->title2, $newText, $suus);
        $success = $result['edit']['result'] ?? '';

        if ($success == 'Success') {
            return true;
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
        test_print("startTranslate():");
        $newText = $this->parseText();

        if (empty($newText) || empty($newText) || $newText == 'n' || $newText == 'notext') {
            test_print('notext');
            return "notext";
        }

        if (strlen($newText) < 1000) {
            test_print('text too short: ' . strlen($newText));
            return 'notext';
        }

        $success = $this->PostToEnwiki($newText);

        return $success;
    }
}

function startTranslatePhp($title, $traType, $return_text, $expend_refs = true)
{
    $wikiTranslator = new WikiTranslator($title, $traType, $expend_refs = $expend_refs);

    if ($return_text) {
        test_print("startTranslatePhp: return_text.");
        return $wikiTranslator->parseText();
    }

    $result = $wikiTranslator->startTranslate();
    if (isset($_GET["go_simple"])) {
        echo <<<HTML
            <script type='text/javascript'>
            window.open('$wikiTranslator->url', '_self');
            </script>
            <noscript>
                <meta http-equiv='refresh' content='0; url={$wikiTranslator->url}'>
            </noscript>
        HTML;
    }
    return $result;
}

function TranslatePhpEditText($text, $expend_refs = true)
{

    $wikiTranslator = new WikiTranslator("", "", $expend_refs = $expend_refs);

    $result = $wikiTranslator->EditTexts($text, $text);

    return $result;
}
