<?php
namespace Model;
use core\QueryBuilder\QueryBuilder;
/**
ORM基类
 */
class Model
{
    public static function table() {
        $query = new QueryBuilder();
        $query->init(\main::getDb(),get_called_class());
        return $query;
    }

    public static function getTableName() {
        $className = get_called_class();
        return strtolower(str_replace('\\','',str_replace('Model','',$className)));//优化
    }

    public static function loadByPk($pk) {
        $query = new QueryBuilder();
        $query->init(\main::getDb(),get_called_class());
        return $query->select(['*'])->from(self::getTableName())->where(['id','=',$pk])->get();
    }

    public static function loadByField($field,$value) {
        $query = new QueryBuilder();
        $query->init(\main::getDb(),get_called_class());
        return $query->select(['*'])->from(self::getTableName())->where([$field,'=',$value])->get();
    }
}