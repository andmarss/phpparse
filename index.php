<?php
// autoload and db requires
require_once __DIR__  . '/vendor/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

use App\Parse;

$parser = new Parse('');

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
                $parser
                    ->set_url('https://moikrug.ru/vacancies?divisions%5B%5D=backend&divisions%5B%5D=frontend&currency=rur&with_salary=1')
                    ->krug_articles();

                $parser->wait(60, 'm');
            }
            break;

        case 'zandz':
            $parser->set_url('https://zandz.com/ru/')->find_images();
            break;

        case 'zandz-show-images':
            $statement = $parser->db->query("SELECT * FROM images");

            if(!$statement->execute()) {
                die(var_dump($statement->errorInfo()));
            }

            $images = $statement->fetchAll(\PDO::FETCH_ASSOC);

            $table = "<table>";
            $table .= "<th>";
            $table .= "<td>src</td>";
            $table .= "</th>";

            foreach ($images as $image){
                $table .= "<tr>";
                $table .= "<td>{$image['src']}</td>";
                $table .= "</tr>";
            }

            $table .= "</table>";

            echo $table;
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
}

$statement = $parser->db->query("SELECT DISTINCT src FROM images");

if(!$statement->execute()) {
    die(var_dump($statement->errorInfo()));
}

$images = $statement->fetchAll(\PDO::FETCH_ASSOC);

$table = "<table>";
$table .= "<tr>";
// $table .= "<th align='center'>id</th>";
$table .= "<th align='center'>src</th>";
$table .= '</tr>';

foreach ($images as $key => $image){
    if(strpos($image['src'], '/') !== 0) continue;

    $i = $key+1;

    preg_match('/[a-z0-9\-\_]+\.[jpg|jpeg|gif|png]+/i', $image['src'], $m);

    if($m && isset($m[0])) {
        $image['src'] = $m[0];
    }

    $table .= "<tr>";
    //$table .= "<td align='center'>{$i}</td>";
    $table .= "<td align='center'>{$image['src']}</td>";
    $table .= "</tr>";
}

$table .= "</table>";

echo $table;