<?php
/**
* redis数据库
 */

namespace Model;


class MemoModel extends Model
{
    protected $id;
    protected $groupId;         //所在群组id
    protected $title;           //标题
    protected $content;         //内容
    protected $specifyUserId;   //指定经办人id
    protected $createUserId;    //创建人id
    protected $status;          //状态
    protected $dtCreate;        //创建时间
    protected $dtUpdate;        //最近更新时间

    protected $errorMsg;        //错误信息

    protected $originField;     //记录原始字段值，在更新时做比较，有修改的字段才更新到数据库

    const TABLE_NAME = 'MEMO';  //键名
    const INCREMENT_COUNT_KEY = 'MEMO_INCREMENT_COUNT';     //记录id自增的键名

    const STATUS_DOING_KEY = 'MEMO_STATUS_DOING';       //进行中的memo记录集合
    const STATUS_COMPLETE_KEY = 'MEMO_STATUS_COMPLETE'; //完成的memo记录集合
    const STATUS_INVALID_KEY = 'MEMO_STATUS_INVALID';   //无效的memo记录集合

    const STATUS_DOING = 1; //进行中
    const STATUS_COMPLETE = 2; //完成
    const STATUS_INVALID = 0; //无效


    /**
     * 根据key获取对象
     * @param $id
     * @return MemoModel|null
     */
    public static function loadByPk($id) {
        $redis = self::getRedis();
        $key = static::getTableName().'-'.$id;
        $v = $redis->getHashAll($key);
        if(empty($v)) {
            return null;
        }
        $obj =  self::init($v);
        $obj->formatKey($id);
        return $obj;
    }

    /**
     * 根据状态获取应该存入的状态列表
     * @param $status
     * @return string
     */
    public static function getStatusKey($status) {
        $arr = [
            self::STATUS_INVALID => self::STATUS_INVALID_KEY,
            self::STATUS_DOING => self::STATUS_DOING_KEY,
            self::STATUS_COMPLETE => self::STATUS_COMPLETE_KEY,
        ];
        return isset($arr[$status])?$arr[$status]:'';
    }

    /**
     * 根据数据初始化对象
     * @param $arr
     * @return MemoModel
     */
    public static function init($arr) {
        $obj = new self();
        foreach($arr as $k=>$v) {
            $obj->$k = $v;
        }
        return $obj;
    }

    /**
     * @param $key
     */
    public function formatKey($key) {
        $this->id = $key;
    }

    /**
     * 获取主键
     * @return string
     */
    public function getKey() {
        return static::getTableName().'-'.$this->id;
    }

    public function getId() {
        return $this->id;
    }

    public function getGroupId() {
        return $this->groupId;
    }

    /**
     * @param $params
     * key 字段名称  value 字段值  问题：字段是protected属性，如何开放给使用者 优化调整
     * @return self
     */
    public static function createModel($params) {
        $model = new self();

        foreach($params as $field => $value) {
            $model->setField($field,$value);
        }
        return $model;
    }


    /**
     * 获取自增的ID
     * @return int
     */
    public static function getMaxId() {
        $redis = self::getRedis();
        $id = $redis->getString(static::INCREMENT_COUNT_KEY);
        return empty($id)?0:intval($id);
    }

    public static function loadListByGroupId($gid) {

    }

    /**
     * 设置字段的值
     * @param $field
     * @param $value
     */
    public function setField($field,$value) {
        $fieldArr = $this->getFieldArr();
        if(array_key_exists($field,$fieldArr)) {
            $this->$field = $value;
        }
    }

    public function setDtCreate($dt) {
        $this->setField('dtCreate',$dt);
    }

    public function setDtUpdate($dt) {
        $this->setField('dtUpdate',$dt);
    }

    /**
     * 更新和保存
     * @return bool
     */
    public function save() {
        $redis = self::getRedis();
        if(empty($this->id)) { //id为空，表示为新记录
            $this->id = self::getMaxId();
            $this->setDtUpdate(time());
            $this->setDtCreate(time());
            $redis->setHashAll($this->getKey(),$this->getFieldArr());
            $redis->setString(static::INCREMENT_COUNT_KEY,($this->id+1));
            $this->adjustStatusList();
            $userGroup = UserGroupModel::loadById($this->groupId);
            $userGroup->addMemo($this->getId());
        } else {
            $redis->setHashAll($this->getKey(),$this->getFieldArr());
            $this->adjustStatusList();  //调整状态列表
        }
        return true;
    }

    /**
     * 调整状态列表
     */
    public function adjustStatusList() {

    }

    public function getContent() {
        return $this->content;
    }

    public function getTitle() {
        return $this->title;
    }

    public static function statusNameList() {
        return [
            self::STATUS_DOING => '进行中',
            self::STATUS_COMPLETE => '完成',
            self::STATUS_INVALID => '无效',
        ];
    }

    public function getStatusName() {
        $arr = self::statusNameList();
        return array_key_exists($this->status,$arr)?$arr[$this->status]:'异常';
    }

    public function getDtCreate() {
        return date('Y-m-d H:i:s',$this->dtCreate);
    }

    public function getFieldArr() {
        return [
            'title' => $this->title,
            'groupId' => $this->groupId,
            'content' => $this->content,
            'specifyUserId' => $this->specifyUserId,
            'createUserId' => $this->createUserId,
            'status' => $this->status,
            'dtCreate' => $this->dtCreate,
            'dtUpdate' => $this->dtUpdate,
        ];
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