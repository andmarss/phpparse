<?php

namespace App\DB;

use \App\Collect;

class PDOCollect extends Collect
{
    public function where($condition, $markOrValue = null, $value = null)
    {
        if (is_array($condition) && (count($condition) > 0)) {
            foreach ($condition as $key => $item){
                return $this->filter(function ($object) use ($key, $item){
                    if(property_exists($object, $key)) {
                        return $object->{$key} == $item;
                    }
                });
            }
        } elseif (is_null($value) && (is_string($condition) && !is_null($markOrValue))) {
            return $this->filter(function ($object) use ($condition, $markOrValue){
                if(property_exists($object, $condition)) {
                    return $object->{$condition} == $markOrValue;
                }
            });
        } elseif (is_string($condition) && !is_null($markOrValue) && !is_null($value)) {
            return $this->filter(function ($object) use ($condition, $markOrValue, $value){
                if(property_exists($object, $condition)) {
                    switch ($markOrValue) {
                        case '>':
                            return $object->{$condition} > $value;
                            break;
                        case '>=':
                            return $object->{$condition} >= $value;
                            break;
                        case '<':
                            return $object->{$condition} < $value;
                            break;
                        case '<=':
                            return $object->{$condition} <= $value;
                            break;
                        case '=':
                            return $object->{$condition} == $value;
                            break;
                        case '!=':
                            return $object->{$condition} != $value;
                            break;
                    }

                }
            });
        }
    }

    public function orWhere($condition, $markOrValue = null, $value = null)
    {
        $this->collection = $this->reserve_collection;

        if (is_array($condition) && (count($condition) > 0)) {
            foreach ($condition as $key => $item){
                return $this->filter(function ($object) use ($key, $item){
                    if(property_exists($object, $key)) {
                        return $object->{$key} == $item;
                    }
                });
            }
        } elseif (is_null($value) && (is_string($condition) && !is_null($markOrValue))) {
            return $this->filter(function ($object) use ($condition, $markOrValue){
                if(property_exists($object, $condition)) {
                    return $object->{$condition} == $markOrValue;
                }
            });
        } elseif (is_string($condition) && !is_null($markOrValue) && !is_null($value)) {
            return $this->filter(function ($object) use ($condition, $markOrValue, $value){
                if(property_exists($object, $condition)) {
                    switch ($markOrValue) {
                        case '>':
                            return $object->{$condition} > $value;
                            break;
                        case '>=':
                            return $object->{$condition} >= $value;
                            break;
                        case '<':
                            return $object->{$condition} < $value;
                            break;
                        case '<=':
                            return $object->{$condition} <= $value;
                            break;
                        case '=':
                            return $object->{$condition} == $value;
                            break;
                        case '!=':
                            return $object->{$condition} != $value;
                            break;
                    }

                }
            });
        }
    }

    public function limit($length, $offset = 0)
    {
        $this->collection = array_slice($this->collection, $offset, $length);

        return $this;
    }
}