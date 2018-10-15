<?php

namespace App;

use App\Interfaces\ArrayAccess;

class Collect implements \Countable, ArrayAccess
{
    protected $collection;

    public function __construct(array $countable)
    {
        $this->collection = $countable;
    }

    public function each(\Closure $func)
    {
        foreach ($this->collection as $key => $item) {
            $func($item, $key, $this->collection);
        }

        return $this;
    }

    public function map(\Closure $func)
    {
        $result = [];

        foreach ($this->collection as $key => $item) {
            $result[] = $func($item, $key, $this->collection);
        }

        $this->collection = $result;

        return $this;
    }

    public function filter(\Closure $func)
    {
        $result = [];

        foreach ($this->collection as $key => $item) {
            if($func($item, $key, $this->collection)) {
                $result[] = $item;
            }
        }

        $this->collection = $result;

        return $this;
    }

    public function search($condition, $strict = false)
    {
        $find = 0;

        if(!is_callable($condition)) {
            if($strict) {
                foreach ($this->collection as $key => $item) {
                    if($condition === $item) {
                        $find++;
                    }
                }
            } else {
                foreach ($this->collection as $key => $item) {
                    if($condition == $item) {
                        $find++;
                    }
                }
            }

            return $find;
        } elseif (is_callable($condition)) {
            return $this->filter($condition)->count();
        }
    }

    public function reject(\Closure $func)
    {
        $result = [];

        foreach ($this->collection as $key => $item) {
            if(!$func($item, $key, $this->collection)) {
                $result[] = $item;
            }
        }

        $this->collection = $result;

        return $this;
    }

    public function reduce(\Closure $func, $initial)
    {
        $accumulator = $initial;

        foreach ($this->collection as $key => $item) {
            $accumulator = $func($accumulator, $item);
        }

        if(is_array($accumulator)) {
            $this->collection = $accumulator;

            return $this;
        }

        return $accumulator;
    }

    public function sum(\Closure $func)
    {
        return $this->reduce(function ($total, $item) use ($func){
            return $total + $func($item);
        }, 0);
    }

    public function count()
    {
        return count($this->collection);
    }

    public static function make($items)
    {
        return new static($items);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->collection);
    }

    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if(!is_null($offset)) {
            $this->collection[] = $offset;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }

    public function every(\Closure $func)
    {
        foreach ($this->collection as $key => $item) {
            if(!$func($item, $key, $this->collection)) {
                return false;
            }
        }

        return true;
    }

    public function some(\Closure $func)
    {
        $i = 0;

        foreach ($this->collection as $key => $item) {
            if($func($item, $key, $this->collection)) {
                $i++;
            }
        }

        return $i !== 0;
    }

    public function get()
    {
        return new static($this->collection);
    }

    public function all()
    {
        return $this->collection;
    }

    public function values()
    {
        $this->collection = array_values($this->collection);

        return $this;
    }

    public function keys()
    {
        $this->collection = array_keys($this->collection);

        return $this;
    }

    public function sortBy()
    {
        $this->collection = array_multisort($this->collection, SORT_ASC, SORT_REGULAR);

        return $this;
    }

    public function sortByDesc()
    {
        $this->collection = array_multisort($this->collection, SORT_DESC, SORT_REGULAR);

        return $this;
    }
}