<?php
namespace App;

class DB
{
    protected $db_handler;

    public function __construct($host, $user, $pw, $db_name)
    {
        $this->db_handler = new \PDO("mysql:host={$host};dbname={$db_name}", $user, $pw);
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
            var_dump($sth->errorInfo() ); die;
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
}