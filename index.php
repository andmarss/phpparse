<?php
// autoload and db requires
require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/app/db/DB.php');

// lib for parsing and db class
use Sunra\PhpSimple\HtmlDomParser;
use App\DB;

/**
 * @param $url
 * @param DB $db
 * @return array
 */

function get_article_data($url, DB $db) {
    // init parser
    $parser = new HtmlDomParser;

    // get content from url
    $hrml = file_get_contents($url);

    // create an object from usual string
    $article = $parser->str_get_html($hrml);

    // get h1 text from article
    $h1 = $db->escape($article->find('h1', 0)->innertext);
    // get content from article
    $content = $db->escape($article->find('article', 0)->innertext);

    $sql = "update articles 
                set h1 = {$h1},
                    content = {$content},
                    date_parsed = NOW()
                where url = '{$url}'";

    $db->query($sql);

    // return h1 and content
    return compact('h1', 'content');
}

/**
 * @param $url
 * @param DB $db
 */

function get_article ($url, DB $db) {
    // init parser
    $parser = new HtmlDomParser;

    // get content from url
    $hrml = file_get_contents($url);

    // create an object from usual string
    $dom = $parser->str_get_html($hrml);

    // get each article link
    foreach ($dom->find('a.read-more-link') as $link) {
        // Each article link - add to db

        $article_url = $db->escape($link->href);

        $sql = "insert ignore into articles set url = {$article_url}";

        $db->query($sql);

        // Parse and save current article by link
        get_article_data($link->href, $db);
    }

    // recursion to next page
    if($next_link = $dom->find('a.next', 0)) {
        get_article($next_link->href, $db);
    }
}

// parsing web source using get_article function

get_article('http://ananaska.com/vse-novosti/', $db);