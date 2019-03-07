<?php
namespace Model;
use core\QueryBuilder\QueryBuilder;
/**
ORM基类
 */
class Model
{
    const TABLE_NAME = '';

    protected $errorMsg;        //错误信息


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

    /**
     * 写入错误消息
     * @param $msg
     */
    public function setErrMsg($msg) {
        $this->errorMsg[] = $msg;
    }

    /**
     * 获取错误消息
     * @return array|string
     */
    public function getErrMsg() {
        return empty($this->errorMsg)?'':implode(',',$this->errorMsg);
    }
}