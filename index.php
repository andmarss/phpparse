<?php

require_once(dirname(__FILE__) . '/vendor/autoload.php');

use Sunra\PhpSimple\HtmlDomParser;

function get_article ($url) {
    $parser = new HtmlDomParser;

    $hrml = file_get_contents($url);

    $dom = $parser->str_get_html($hrml);

    foreach ($dom->find('a.read-more-link') as $link) {
        echo $link->href . PHP_EOL;
    }

    if($next_link = $dom->find('a.next', 0)) {
        get_article($next_link->href);
    }
}

get_article('http://ananaska.com/vse-novosti/');