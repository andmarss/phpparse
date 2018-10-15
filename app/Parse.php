<?php
namespace App;

/**
 * Created by PhpStorm.
 * User: delux
 * Date: 12.10.2018
 * Time: 17:06
 */
use Sunra\PhpSimple\HtmlDomParser;
use App\DB\DB;
use App\Model\Article;

class Parse
{
    protected $url;
    protected $parser;
    protected $article;
    protected $html;

    public function __construct(string $url = '')
    {
        $this->url = $url;
        $this->parser = new HtmlDomParser();
    }

    public function get_article_data()
    {
        // get content from url
        $html = file_get_contents($this->url);

        // create an object from usual string
        $article = $this->parser->str_get_html($html);

        Article::create([
            'h1' => $article->find('h1', 0)->innertext,
            'content' => $article->find('article', 0)->innertext,
            'date_parsed' => date('Y-m-d H:i:s'),
            'url' => $this->url
        ]);

        return $this;
    }

    public function set_url(string $url = '')
    {
        $this->url = $url;

        return $this;
    }

    public function get_articles()
    {
        // get content from url
        $hrml = file_get_contents($this->url);

        // create an object from usual string
        $dom = $this->parser->str_get_html($hrml);

        // get each article link
        foreach ($dom->find('a.read-more-link') as $link) {
            // Each article link - add to db

            //save new href
            $this->set_url($link->href);

            // Parse and save current article by link
            $this->get_article_data();
        }

        // recursion to next page
        if($next_link = $dom->find('a.next', 0)) {
            $this->set_url($next_link->href);

            $this->get_articles();
        }
    }

    public function set_tmp_uniq()
    {
        Article::set_tmp_uniq(function ($articles){
            foreach ($articles as $article) {
                $this->set_url($article->url);

                $this->get_article_data();
            }
        });
    }
}