<?php

namespace App\DB;

use \App\Collect;

class PDOCollect extends Collect
{
    protected $table;

    public function where(...$condition)
    {
        if (is_array($condition) && (count($condition) > 0)) {
            foreach ($condition as $key => $item){
                $this->collection = $this->filter(function ($object) use ($key, $item){
                    if(property_exists($object, $key)) {
                        return $object->{$key} == $item;
                    }
                });
            }
        }

        return $this;
    }

    public function limit($length, $offset = 0)
    {
        $this->collection = array_slice($this->collection, $offset, $length);

        return $this;
    }
}