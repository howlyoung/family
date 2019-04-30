<?php
namespace core\Model;
/**
ORM基类
 */
abstract class Model
{
    const TABLE_NAME = '';

    protected $errorMsg;        //错误信息

    protected $_attributes; //字段数组

    protected $_oldAttributes; //旧的字段值

    public function __construct() {

    }

    public static function table() {
        return null;
    }

    protected function getPrefix() {
        return '';
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
     * 判断是否新记录，true 新记录
     * @return bool
     */
    public function isNewRecord() {
        return empty($this->_oldAttributes);
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

    protected function insertRecord() {

    }

    protected function updateRecord() {

    }

    public function save() {

    }

    public function update() {

    }

    public function delete() {

    }

    public function fill($row) {
        //公共方法，可能会被外部调用，优化!
        $attributes = $this->attributes();
        foreach($row as $k=>$v) {
            if(array_key_exists($k,$attributes)) {
                $this->_attributes[$k] = $v;
            }
        }
        $this->_oldAttributes = $this->_attributes;
    }

}