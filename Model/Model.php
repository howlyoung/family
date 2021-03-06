<?php
namespace Model;
/**
ORM基类
 */
class Model
{
    const TABLE_NAME = '';

    protected $errorMsg;        //错误信息

    protected $query;      //查询器

    protected $_isNewRecord; //是否新记录,false则不是新记录  优化!

    public function __construct() {
        $this->query = static::table();
    }

    protected static function table() {
        return null;
    }

    public static function getTableName() {
        return static::TABLE_NAME;
    }

    /**
     * 设置为
     */
    public function setNewRecord() {
        if($this->_isNewRecord != false) {
            $this->_isNewRecord = false;
        }
    }

    public function getIsNewRecord() {
        return $this->_isNewRecord==false ? false:true;
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

    public function save() {

    }

    public function update() {

    }

    public function delete() {

    }

}