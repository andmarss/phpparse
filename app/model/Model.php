<?php
namespace App;

use \App\DB;

abstract class Model
{
    public static $table;

    public $id;

    protected $db;
    protected static $database;

    public function __construct()
    {
        static::$database = require_once $_SERVER['DOCUMENT_ROOT'] . '/app/bootstrap.php';
        $this->db = require_once $_SERVER['DOCUMENT_ROOT'] . '/app/bootstrap.php';
    }

    public static function findAll()
    {
        $data = static::$database->query(
            'SELECT * FROM' . static::$table,
            [],
            static::class
        );
        return $data;
    }

    public static function findById($id)
    {
        $sql = 'SELECT * FROM ' . static::$table . ' WHERE id=:id';
        $data = static::$database->query($sql, [':id' => $id], static::class);
        return $data[0] ?? false;
    }

    public function isNew()
    {
        return empty($this->id);
    }

    public function insert()
    {
        if($this->isNew()){

            $columns = [];
            $binds= [];
            $data = [];

            foreach($this as $column => $value){
                if($column == 'id') continue;

                $columns[] = $column;
                $binds[] = ':' . $column;
                $data[':' . $column] = $value;
            }

            $sql = '
           INSERT INTO ' . static::$table . '
	   (' . implode(', ', $columns) . ')
           VALUES
	   (' . implode(', ', $binds) . ')
           ';

            static::$database->execute($sql, $data);
            $this->id = static::$database->lastInsertId();
        }
    }

    public function update()
    {
        if($this->isNew()){
            $columns = [];
            $data = [];

            foreach($this as $column => $value){
                if($column == 'id') continue;
                $data[] = $column . ' = ' . $value;
            }

            $sql = '
                 UPDATE FROM ' . static::table . '
                 SET ' . implode(', ', $data) . '
                 WHERE id = ' . $this->id;

            $result = static::$database->execute($sql);

            return $result;
        }
    }

    public function delete()
    {
        if(isset($this->id)){
            $sql = '
         DELETE FROM ' . static::table . '
         WHERE id = ' . $this->id;

            $result = static::$database->execute($sql);

            return $result;
        }
    }

    public function save()
    {
        if($this->isNew()) $this->insert();
        else $this->update();
    }
}
