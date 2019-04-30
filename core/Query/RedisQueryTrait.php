<?php
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/4/16
 * Time: 9:33
 */

namespace core\Query;


trait RedisQueryTrait
{
    protected static function table() {
        return \main::getDb('redis');
    }
}