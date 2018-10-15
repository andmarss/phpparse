<?php
/**
 * Created by PhpStorm.
 * User: delux
 * Date: 15.10.2018
 * Time: 17:11
 */

namespace App\DB;

use \App\Collect;


class PDOCollect extends Collect
{
    protected $collection;

    public function __construct(\Countable $countable)
    {
        $this->collection = $countable;
    }

    public function where($condition, $value = null)
    {
        if (is_array($condition) && (count($condition) > 0)) {

        }
    }
}