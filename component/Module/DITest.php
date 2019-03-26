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

    public function __construct(\component\Module\DIdpTest $t) {
        $this->t = $t;
    }
}