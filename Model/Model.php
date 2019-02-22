<?php
namespace Model;
use core\QueryBuilder\QueryBuilder;
/**
ORM基类
 */
class Model
{
    const TABLE_NAME = '';

    public static function table() {
        $query = new QueryBuilder();
        $query->init(\main::getDb(),get_called_class());
        return $query;
    }

    public static function getTableName() {
//        $className = get_called_class();
//        return strtolower(str_replace('\\','',str_replace('Model','',$className)));//优化
        return static::TABLE_NAME;
    }

    /**
     * @return \core\Db\DbRedis
     */
    public static function getRedis() {
        return \main::getDb('redis');
    }
}