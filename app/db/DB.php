<?php
namespace App;

class DB
{
    protected $db_handler;
    protected static $pdo;

    public function __construct($config)
    {
        $this->db_handler = new \PDO($config['connection'] . $config['name'], $config['username'], $config['password'], $config['options']);
        static::$pdo = new \PDO($config['connection'] . $config['name'], $config['username'], $config['password'], $config['options']);
    }

    public function execute(string $sql, array $data = [])
    {
        $sth = $this->db_handler->prepare($sql);

        $result = $sth->execute($data);

        if(!$result) {
            var_dump($sth->errorInfo()); die();
        }

        return true;
    }

    public function query(string $sql, array $data = [], $class = null)
    {
        $sth = $this->db_handler->prepare($sql);

        $result = $sth->execute($data);

        if(!$result) {
            var_dump($sth->errorInfo()); die;
        }

        return (is_null($class)) ? $sth->fetchAll() : $sth->fetchAll(\PDO::FETCH_CLASS, $class);
    }

    public function lastInsertId()
    {
        return $this->db_handler->lastInsertId();
    }

    public function escape(string $string)
    {
        return $this->db_handler->quote($string);
    }

    public function selectAll($table, $intoClass = null)
    {
        $statement = $this->db_handler->prepare("select * from {$table}");

        $statement->execute();

        return isset($intoClass)
            ?
            $statement->fetchAll(\PDO::FETCH_CLASS, $intoClass)
            :
            $statement->fetchAll(\PDO::FETCH_CLASS);
    }

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

    public static function escape_string(string $string)
    {
        return static::$pdo->quote($string);
    }
}