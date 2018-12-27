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
}