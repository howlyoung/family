<?php
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/2/20
 * Time: 10:05
 */
namespace core\Db;

interface DbInterface {

    public function getDb($config);
}