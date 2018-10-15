<?php
/**
 * Created by PhpStorm.
 * User: delux
 * Date: 15.10.2018
 * Time: 17:14
 */

namespace App;


class Collect implements \Countable
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

        return new static($result);
    }

    public function filter(\Closure $func)
    {
        $result = [];

        foreach ($this->collection as $key => $item) {
            if($func($item, $key, $this->collection)) {
                $result[] = $item;
            }
        }

        return new static($result);
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
            return count($this->filter($condition));
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

        return new static($result);
    }

    public function reduce(\Closure $func, $initial)
    {
        $accumulator = $initial;

        foreach ($this->collection as $key => $item) {
            $accumulator = $func($accumulator, $item);
        }

        return is_array($accumulator) ? new static($accumulator) : $accumulator;
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
}