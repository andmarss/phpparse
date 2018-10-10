<?php

require_once(dirname(__FILE__) . '/vendor/autoload.php');

use Sunra\PhpSimple\HtmlDomParser;

$parser = new HtmlDomParser;

$hrml = file_get_contents('http://ananaska.com/vse-novosti/');

$dom = $parser->str_get_html($hrml);

var_dump($dom->innertext);