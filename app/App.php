<?php
namespace App;
/**
 * Created by PhpStorm.
 * User: delux
 * Date: 12.10.2018
 * Time: 17:18
 */

class App
{
    protected static $registry = [];

    public static function bind($key, $value)
    {
        static::$registry[$key] = $value;
    }

    public static function get($key)
    {
        if(!array_key_exists($key, static::$registry)) {
            throw new \Exception("No \"{$key}\" is bound in the container.");
        }

        return static::$registry[$key];
    }
}