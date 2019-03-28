<?php
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/3/26
 * Time: 16:08
 */

namespace component\Module;


class DITest
{
    protected $t;
    protected $a;

    public function __construct($t,DIInterface $a) {
        $this->t = $t;
        $this->a = $a;
    }
}