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

            // get random hash
            $tmp_uniq = md5(uniqid() . time());

            DB::query("UPDATE articles SET tmp_uniq = '{$tmp_uniq}' WHERE tmp_uniq IS NULL LIMIT " . DB::escape_string(App::get('per_block')));

            $parser->get_article_data();
            break;
    }
}