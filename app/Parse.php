<?php
/**
 * Created by PhpStorm.
 * User: delux
 * Date: 12.10.2018
 * Time: 17:06
 */
use Sunra\PhpSimple\HtmlDomParser;
use \App\DB;

require_once(dirname(__FILE__) . '../config.php');

class Parse
{
    protected $url;
    protected $parser;
    protected $article;

    public function __construct($url)
    {
        $this->url = $url;
        $this->parser = new HtmlDomParser();
        $this->article = new \App\Article();
    }

    public function get_article()
    {
        // get content from url
        $hrml = file_get_contents($this->url);

        // create an object from usual string
        $article = $this->parser->str_get_html($hrml);

        // get h1 text from article
        $h1 = DB::escape_string($article->find('h1', 0)->innertext);
        // get content from article
        $content = DB::escape_string($article->find('article', 0)->innertext);

        
    }
}