<?php
namespace App\DB;

class DB
{
    protected $db_handler;
    protected static $pdo;

    public function __construct($config)
    {
        static::$pdo = new \PDO($config['connection'] . $config['name'], $config['username'], $config['password'], $config['options']);
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

        $result = $sth->execute($data);

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

        return (is_null($class)) ? $sth->fetchAll() : $sth->fetchAll(\PDO::FETCH_CLASS, $class);
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

    /**
     * @param $table
     * @param null $intoClass
     * @return mixed
     *
     * Function to find a collection of objects from DB
     */

    public function all($table, $intoClass = null)
    {
        $statement = $this->db_handler->prepare("select * from {$table}");

        $statement->execute();

        return isset($intoClass)
            ?
            $statement->fetchAll(\PDO::FETCH_CLASS, $intoClass)
            :
            $statement->fetchAll(\PDO::FETCH_CLASS);
    }

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

    protected static function get_table($name, $class = null)
    {
        $pdo_statement = static::$pdo->prepare('SELECT * FROM ' . $name);

        $result = $pdo_statement->execute();

        if(!$result) {
            var_dump($pdo_statement->errorInfo()); die();
        }

        return (!$class) ? $pdo_statement->fetchAll(\PDO::FETCH_CLASS, static::class) : $pdo_statement->fetchAll(\PDO::FETCH_CLASS, $class);
    }

    static function table($name)
    {
        return static::get_table($name, static::class);
    }


}