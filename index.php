<?php
// autoload and db requires
require_once __DIR__  . '/vendor/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

use \App\Parse;

$parser = new Parse('http://ananaska.com/vse-novosti/');

$parser->get_articles();