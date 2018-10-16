<?php
namespace App\DB;

use App\DB\PDOCollect;
use App\Article;

class DB extends PDOCollect
{
    protected $db_handler;
    protected static $pdo;

    public function __construct($config)
    {
        parent::__construct([]);
        static::$pdo = new \PDO($config['connection'] . $config['name'], $config['username'], $config['password'], $config['options']);
        static::$pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $this->db_handler = static::$pdo;
    }

    /**
     * @param string $sql
     * @param array $data
     * @return bool
     *
     * Get the sql and data, and execute a request to DB, using PDO class
     */

    public function execute(string $sql, array $data = [])
    {
        $sth = $this->db_handler->prepare($sql);

        $result = (count($data) > 0) ? $sth->execute($data) : $sth->execute();

        if(!$result) {
            var_dump($sth->errorInfo()); die();
        }

        return true;
    }

    /**
     * @param string $sql
     * @param array $data
     * @param null $class
     * @return mixed
     *
     * As same, as an execute, but return an array of arrays, or a collection of objects from DB data
     */

    public function query(string $sql, array $data = [], $class = null)
    {

        $sth = $this->db_handler->prepare($sql);

        $result = $sth->execute($data);

        if(!$result) {
            var_dump($sth->errorInfo()); die;
        }

        return (is_null($class)) ? $sth->fetchAll(\PDO::FETCH_CLASS) : $sth->fetchAll(\PDO::FETCH_CLASS, $class);
    }

    public static function query_string(string $sql, array $data = [], $class = null)
    {
        try {
            static::$pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);

            $sth = static::$pdo->prepare($sql);

            $result = $sth->execute($data);

            if(!$result) {
                var_dump($sth->errorInfo()); die;
            };

            return $sth;
        } catch (\Exception $e) {
            echo print_r($e->getMessage(), true) . "\n";
            echo print_r($e->getTraceAsString(), true) . "\n";
            echo print_r($e->getFile(), true) . "\n";
            echo print_r($e->getCode(), true) . "\n";
            echo print_r($e->getLine(), true) . "\n"; die;
        }
    }

    /**
     * @return mixed
     *
     * return last inserted id
     */

    public function lastInsertId()
    {
        return $this->db_handler->lastInsertId();
    }

    /**
     * @param string $string
     * @return mixed
     *
     * Quotes a string for use in a query
     */

    public function escape(string $string)
    {
        return $this->db_handler->quote($string);
    }

//    /**
//     * @param $table
//     * @param null $intoClass
//     * @return mixed
//     *
//     * Function to find a collection of objects from DB
//     */
//
//    public function all($table, $intoClass = null)
//    {
//        $statement = $this->db_handler->prepare("select * from {$table}");
//
//        $statement->execute();
//
//        return isset($intoClass)
//            ?
//            $statement->fetchAll(\PDO::FETCH_CLASS, $intoClass)
//            :
//            $statement->fetchAll(\PDO::FETCH_CLASS);
//    }

    /**
     * @param $table
     * @param $data
     *
     * Function to insert attributes of current article object to DB
     */

    public function insert($table, $data)
    {
        $sql = sprintf(
            'insert into %s ($s) values (%s)',
            $table,
            implode(', ', array_keys($data)),
            ':' . implode(', :', array_keys($data))
        );

        try {
            $statement = $this->db_handler->prepare($sql);

            $statement->execute($data);
        } catch (\Exception $e) {
            die('Whoops, something went wrong');
        }
    }

    /**
     * @param string $string
     * @return mixed
     *
     * Quotes a string for use in a query
     */

    public static function escape_string(string $string)
    {
        return static::$pdo->quote($string);
    }

    /**
     * @param $name
     * @param null $class
     * @return mixed
     *
     * Function to find a collection of objects from DB
     */

    protected static function get_table($name, $class = null)
    {
        $pdo_statement = static::$pdo->prepare('SELECT * FROM ' . $name);

        $result = $pdo_statement->execute();

        if(!$result) {
            var_dump($pdo_statement->errorInfo()); die();
        }

        return (!$class) ? $pdo_statement->fetchAll(\PDO::FETCH_CLASS) : $pdo_statement->fetchAll(\PDO::FETCH_CLASS, $class);
    }

    static function table($name)
    {
        return PDOCollect::make(static::get_table($name, 'App\\' . substr(ucfirst($name), 0, mb_strlen($name)-1)));
    }

    protected function collect(array $array)
    {
        $this->collection = new PDOCollect($array);

        return $this;
    }
}