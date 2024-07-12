<?php

namespace WikiParse\Template;
// include_once __DIR__ . '/../WikiParse/Template.php';
include_once __DIR__ . '/../vendor_load.php';
use WikiConnect\ParseWiki\ParserTemplate;

function getTemplate($text)
{
    $parser = new ParserTemplate($text);
    $temp = $parser->getTemplate();
    return $temp;
}
