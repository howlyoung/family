<?php
namespace core\Model;
/**
ORM基类
 */
class Model
{
    const TABLE_NAME = '';

    protected $errorMsg;        //错误信息

    protected $query;      //查询器

    protected $_isNewRecord; //是否新记录,false则不是新记录  优化!

    protected $_attributes; //字段数组

    public function __construct() {
        $this->query = static::table();
    }

    protected static function table() {
        return null;
    }

    /**
     * 字段的键名
     * @return array
     */
    public function attributes() {
        return [];
    }

    public static function getTableName() {
        return static::TABLE_NAME;
    }

    public function __get($name) {
        if(isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        }
        return null;
    }

    public function __set($name,$value) {
        if(isset($this->attributes()[$name])) {
            $this->_attributes[$name] = $value;
        }
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

    public function fill($row) {
        $attributes = $this->attributes();
        foreach($row as $k=>$v) {
            if(array_key_exists($k,$attributes)) {
                $this->_attributes[$k] = $v;
            }
        }
    }

}