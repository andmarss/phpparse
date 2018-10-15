<?php
/**
 * Created by PhpStorm.
 * User: delux
 * Date: 12.10.2018
 * Time: 18:47
 */

namespace App\Model;

use App\App;
use App\Model\Model;
use App\DB\DB;


class Article extends Model
{
    public static $table = 'articles';

    public static function set_tmp_uniq(\Closure $callback)
    {
        while (true) {

            //get random hash
            $tmp_uniq = md5(uniqid() . time());

            DB::query_string('UPDATE ' . static::$table . ' SET tmp_uniq = ' . DB::escape_string($tmp_uniq) . ' WHERE tmp_uniq IS NULL LIMIT ' . App::get('PER_BLOCK'));

            $articles = DB::query_string('SELECT url FROM ' . static::$table . ' WHERE tmp_uniq = ' . DB::escape_string($tmp_uniq))->fetchAll(\PDO::FETCH_CLASS, static::class);

            echo print_r($articles);

            if(count($articles) === 0) {
                echo print_r('All done!'); die();
            }

            $callback($articles);
        }


    }
}