<?php

namespace core;
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/3/27
 * Time: 14:42
 */
class Instance
{
    protected $id;

    protected function __construct($id) {
        $this->id = $id;
    }

    public static function of($id) {
        return new static($id);
    }

    public function getId() {
        return $this->id;
    }
}