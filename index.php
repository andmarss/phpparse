<?php

require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/app/db/DB.php');


use Sunra\PhpSimple\HtmlDomParser;
use App\DB;

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'parse');

$db = new DB(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

var_dump($db->query('show tables')); die;

function get_article_data($url) {
    $parser = new HtmlDomParser;

    $hrml = file_get_contents($url);

    $article = $parser->str_get_html($hrml);

    $h1 = $article->find('h1', 0)->innertext;
    $content = $article->find('article', 0)->innertext;

    return compact('h1', 'content');
}

function get_article ($url) {
    $parser = new HtmlDomParser;

    $hrml = file_get_contents($url);

    $dom = $parser->str_get_html($hrml);

    foreach ($dom->find('a.read-more-link') as $link) {
        print_r(get_article_data($link->href));
    }

    if($next_link = $dom->find('a.next', 0)) {
        get_article($next_link->href);
    }
}

get_article('http://ananaska.com/vse-novosti/');