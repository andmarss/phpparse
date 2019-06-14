<?php

use App\DB\DB;
use App\App;

 App::bind('config' , require_once(__DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/../config.php')));

//require_once $_SERVER['DOCUMENT_ROOT'] . '/app/Router.php';
//
//require_once $_SERVER['DOCUMENT_ROOT'] . '/app/Request.php';
//
//require_once 'database/Connection.php';
//
//require_once 'database/QueryBuilder.php';

//App::bind('database', new DB(App::get('config')['DB']));

App::bind('per_block', 5);
