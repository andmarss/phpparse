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
use App\Article;

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

    public function get_lugashop_page_info()
    {
        // get content from url
        $hrml = file_get_contents($this->url);

        // create an object from usual string
        $dom = $this->parser->str_get_html($hrml);

        echo trim($dom->find('div.stocks span', 0)->plaintext) . "\n";
    }

    public function span_found()
    {
        $hrml = file_get_contents($this->url);

        // create an object from usual string
        $dom = $this->parser->str_get_html($hrml);

        return count($dom->find('div.stocks span')) > 0;
    }

    public function krug_articles()
    {
        $hrml = file_get_contents($this->url);

        // create an object from usual string
        $dom = $this->parser->str_get_html($hrml);
        $skills = ['php', 'javascript', 'react.js', 'react', 'redux', 'laravel'];

        foreach ($dom->find('div.job') as $job) {
            $title = trim($job->find('.title a')->plaintext);
            $specialization = $job->find('.specialization a');
            $skills_list = $job->find('.skills .skill');
            $salary = $job->find('.salary .count b');

            echo 'Вакансия: ' . $title . "\n";

            echo 'Специализация: ' . "\n";

            if(count($specialization) > 0) {
                foreach ($specialization as $key => $value) {
                    echo ($key + 1) . ') ' . trim($value->plaintext) . "\n";
                }
            }

            echo 'Требуемые навыки: ' . "\n";

            if(count($skills_list) > 0) {
                foreach ($skills_list as $key => $s) {
                    echo ($key + 1) . ') ' . trim($s->plaintext) . "\n";
                }
            }

            echo 'Зарплата: ' . "\n";

            foreach ($salary as $b) {
                echo trim($b->plaintext) . "\n";
            }

            echo "\n\n";

            echo '============================' . "\n\n\n";
        }

        if($next_link = $dom->find('a.next_page', 0)) {
            if(isset($next_link->href)) {
                echo 'Следующая страница найдена ' . "\n";
                $this->set_url('https://moikrug.ru' . $next_link->href);

                $this->krug_articles();
            }
        } else {
            echo 'Next link not found'; die;
        }
    }

    public function wait($num, $mod = 's')
    {
        switch ($mod) {
            case 'ms':
                sleep($num / 1000);
                break;
            case 's':
                sleep($num);
                break;
            case 'm':
                sleep($num * 60);
                break;
        }

        return $this;
    }
}