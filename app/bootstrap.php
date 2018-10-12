<?php

use \App\DB;

App::bind('config' , require $_SERVER['DOCUMENT_ROOT'] . '/config.php');

//require_once $_SERVER['DOCUMENT_ROOT'] . '/app/Router.php';
//
//require_once $_SERVER['DOCUMENT_ROOT'] . '/app/Request.php';
//
//require_once 'database/Connection.php';
//
//require_once 'database/QueryBuilder.php';

App::bind('database', new DB(App::get('config')['database']));
