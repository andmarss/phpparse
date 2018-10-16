<?php

namespace App\DB;

use \App\Collect;

class PDOCollect extends Collect
{
    public function where(...$condition)
    {
        if (is_array($condition) && (count($condition) > 0)) {
            foreach ($condition as $key => $item){
                return $this->filter(function ($object) use ($key, $item, $condition){
                    if(property_exists($object, $item)) {
                        return $object->{$item} == $condition[$key+1];
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