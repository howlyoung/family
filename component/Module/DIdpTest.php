<?php
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/3/26
 * Time: 16:08
 */

namespace component\Module;



class DIdpTest implements DIInterface
{
    protected $a;

    public function __construct($a) {
        $this->a = $a;
    }

    public function test() {

    }
}