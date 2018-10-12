<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13.06.2018
 * Time: 12:28
 */

class QueryBuilder
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function selectAll($table, $intoClass = null)
    {
        $statement = $this->pdo->prepare("select * from {$table}");

        $statement->execute();

        return isset($intoClass)
            ?
            $statement->fetchAll(PDO::FETCH_CLASS, $intoClass)
            :
            $statement->fetchAll(PDO::FETCH_CLASS);
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
            $statement = $this->pdo->prepare($sql);

            $statement->execute($data);
        } catch (Exception $e) {
            die('Whoops, something went wrong');
        }
    }
}
