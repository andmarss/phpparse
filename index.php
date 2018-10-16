<?php
// autoload and db requires
require_once __DIR__  . '/vendor/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

use App\Parse;
use App\DB\DB;

$parser = new Parse('https://moikrug.ru/vacancies?_=1539728509237&city_id=&company_id=&company_name=&currency=rur&division_ids%5B%5D=&divisions%5B%5D=backend&divisions%5B%5D=frontend&employment_type=&location=&q=&qid=4&remote=1&salary=&skills_finder=&type=all&utf8=%E2%9C%93');

if($argv[1] === 'parse') {
    switch ($argv[2]) {
        case 'catalog':
            $parser->get_articles();
            break;
        case 'article':

            $articles = DB::table('articles')->where('tmp_uniq', null)->get();

            if($articles->count() === 0) {
                echo 'All done!'; die;
            }

            $articles->chunk(10)->each(function ($chunk) use ($parser) {
                // get random hash
                $tmp_uniq = md5(uniqid() . time());

                foreach ($chunk as $article) {
                    $article->tmp_uniq = $tmp_uniq;

                    $article->save();
                }
            });

            echo 'All done!'; die;

            break;

        case 'lugashop':
            if($parser->span_found()) {
                while (true) {
                    $parser->get_lugashop_page_info();

                    $parser->wait(10, 'm');
                }
            }
            break;

        case 'moikrug':
            while (true) {
                $parser->krug_articles();

                $parser->wait(60, 'm');
            }
            break;

        default:
            echo 'Command "parse ' . $argv[2] . '" not found';
            break;
    }
} elseif ($argv[1] === 'show'){
    switch ($argv[2]) {
        case 'id':

            DB::table('articles')

                ->where('id', '>=', 50)

                ->sortBy('id')

                ->limit(20)

                ->each(function ($article){
                    echo $article->id . "\n";
                })

                ->orWhere('id', '>=', 110)

                ->sortByDesc('id')

                ->limit(10)

                ->each(function ($article){
                    echo 'Or where: ' . $article->id . "\n";
                });

            break;

        default:
            echo 'Command "show ' . $argv[2] . '" not found';
            break;
    }
} else {
    echo 'Command "' . $argv[1] . '" not found';
}