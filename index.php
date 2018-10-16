<?php
// autoload and db requires
require_once __DIR__  . '/vendor/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

use App\Parse;
use App\DB\DB;

$parser = new Parse('http://ananaska.com/vse-novosti/');

if($argv[1] === 'parse') {
    switch ($argv[2]) {
        case 'catalog':
            $parser->get_articles();
            break;
        case 'article':

            $articles = DB::table('articles')->where('tmp_uniq', null)->get();

            if(!$articles) {
                echo 'All done!'; die;
            }

            $articles->chunk(10)->each(function ($chunk) use ($parser) {
                // get random hash
                $tmp_uniq = md5(uniqid() . time());

                foreach ($chunk as $article) {
                    $article->tmp_uniq = $tmp_uniq;

                    $article->save();

                    $parser->set_url($article->url);

                    $parser->get_article_data();
                }
            });

            break;
    }
}