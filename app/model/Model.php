<?php
namespace App\Model;

use \App\App;

abstract class Model
{
    public static $table;

    public $id;
    protected static $database;

    public function __construct()
    {
        static::$database = require_once(__DIR__ . '/../bootstrap.php');
    }

    /**
     * @return mixed
     *
     * Function to find a collection of objects from DB
     */

    public static function all()
    {
        $data = static::$database->query(
            'SELECT * FROM' . static::$table,
            [],
            static::class
        );
        return $data;
    }

    /**
     * @param $id
     * @return bool
     *
     * Function for search an object in DB by id attribute
     */

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

    /**
     * @return $this
     *
     * Function to insert attributes of current article object to DB
     */

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
           INSERT ignore INTO ' . static::$table . '
           (' . implode(', ', $columns) . ')
               VALUES
           (' . implode(', ', $binds) . ')
               ';

            static::$database->execute($sql, $data);

            $this->id = static::$database->lastInsertId();
        }

        return $this;
    }

    /**
     * @param null $condition
     * @param null $value
     * @return mixed
     *
     * Function for update current article object
     * first and second parameters can be a strings of attributes
     * or first parameter can be an array of attributes
     */

    public function update($condition = null, $value = null)
    {
        var_dump('update');
        if (is_array($condition)) {
            $columns = [];
            $data = [];

            foreach($this as $column => $value){
                if($column == 'id') continue;
                $data[] = $column . ' = :' . $column;
                $columns[':' . $column] = $value;
            }

            reset($condition);

            $whereKey = key($condition);
            $whereValue = $condition[$whereKey];

            $sql = '
                 UPDATE ' . static::$table . '
                 SET ' . implode(', ', $data) . '
                 WHERE ' . $whereKey .' = ' . static::$database->escape_string($whereValue);

            $result = static::$database->execute($sql, $columns);

            return $result;
        } elseif (!is_null($condition) && !is_null($value)) {
            $columns = [];
            $data = [];

            foreach($this as $column => $value){
                if($column == 'id') continue;
                $data[] = $column . ' = :' . $column;
                $columns[':' . $column] = $value;
            }

            $sql = '
                 UPDATE ' . static::$table . '
                 SET ' . implode(', ', $data) . '
                 WHERE ' . $condition .' = ' . static::$database->escape_string($value);

            $result = static::$database->execute($sql, $columns);

            return $result;
        } else {
            $columns = [];
            $data = [];

            foreach($this as $column => $value){
                if($column == 'id') continue;
                $data[] = $column . ' = :' . $column;
                $columns[':' . $column] = $value;
            }

            $sql = '
                 UPDATE ' . static::$table . '
                 SET ' . implode(', ', $data) . '
                 WHERE id = ' . $this->id;

            $result = static::$database->execute($sql, $columns);

            return $result;
        }
    }

    /**
     * @param array $whereArray
     * @param array $updateData
     * @return $this
     *
     * то же, что и пре
     */

    public function updateWhere(array $whereArray = [], array $updateData = [])
    {
        var_dump('updateWhere');
        if($this->isNew()){
            $columns = [];
            $data = [];

            foreach($this as $column => $value){
                if($column == 'id') continue;

                $data[] = $column . ' = :' . $column;
                $columns[':' . $column] = $value;
            }

            if(!is_null($updateData) && count($updateData) > 0) {
                foreach ($updateData as $column => $value) {
                    if($column == 'id') continue;

                    $data[] = $column . ' = :' . $column;
                    $columns[':' . $column] = $value;
                }
            }

            $whereKey = '';
            $whereValue = '';
            $i = 1;

            foreach ($whereArray as $key => $value) {
                if($i > 1) break;

                $whereKey = $key;
                $whereValue = $value;

                $i++;
            }

            $sql = '
                 UPDATE ' . static::$table .
                ' SET ' . implode(', ', $data) .
                ' WHERE '. $whereKey .' = ' . static::$database->escape_string($whereValue);

            static::$database->execute($sql, $columns);

            return $this;
        }
    }

    /**
     * @return mixed
     *
     * Function to delete current object from DB
     */

    public function delete()
    {
        if(isset($this->id)){
            $sql = '
                     DELETE FROM ' . static::$table . '
                     WHERE id = ' . $this->id;

            $result = static::$database->execute($sql);

            return $result;
        }
    }

    /**
     * Function to save current object attributes to DB
     */

    public function save()
    {
        if($this->isNew()) $this->insert();
        else $this->update();
    }

    /**
     * @param array $attributes
     * @return bool
     *
     * Static function to create a record in the database from the array
     */

    public static function create(array $attributes = [])
    {
        $columns = [];
        $binds= [];
        $data = [];
        static::$database = App::get('database');

        foreach($attributes as $column => $value){
            if($column == 'id') continue;

            $columns[] = $column;
            $binds[] = ':' . $column;
            $data[':' . $column] = $value;
        }

        $sql = 'INSERT ignore INTO ' . static::$table . '
               (' . implode(', ', $columns) . ')
                   VALUES
               (' . implode(', ', $binds) . ')';

        $result = static::$database->execute($sql, $data);

        echo print_r('RESULT' . $result, true) . "\n";

        $id = static::$database->lastInsertId();

        $sql = 'SELECT * FROM ' . static::$table . ' WHERE id=:id';

        $data = static::$database->query($sql, [':id' => $id], static::class);

        return $data[0] ?? false;
    }
}
