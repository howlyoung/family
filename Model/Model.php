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

    /**
     * @param $pk
     * @return mixed
     */
    public static function loadByPk($pk) {
        $query = new QueryBuilder();
        $query->init(\main::getDb(),get_called_class());
        return $query->select(['*'])->from(self::getTableName())->where(['id','=',$pk])->get();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public static function loadByField(Array $params) {
        $query = new QueryBuilder();
        $query->init(\main::getDb(),get_called_class());
        $where = [];
        foreach($params as $k=>$v) {
            $where[] = [$k,'=',$v];
        }
        return $query->select(['*'])->from(self::getTableName())->where($where)->get();
    }
}