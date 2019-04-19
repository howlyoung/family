<?php
namespace core\Model\RedisModel;
use core\Model\Model;

/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/4/19
 * Time: 9:30
 */
class RedisModel extends Model
{
    protected static function table() {
        return \main::getDb('redis');
    }
}