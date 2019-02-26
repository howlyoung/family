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

    protected $originField;     //记录原始字段值，在更新时做比较，有修改的字段才更新到数据库

    const TABLE_NAME = 'MEMO';
    const INCREMENT_COUNT_KEY = 'MEMO_INCREMENT_COUNT';

    public static function loadByPk($id) {
        $redis = self::getRedis();
        $key = static::getTableName().'-'.$id;
        $v = $redis->getHashAll($key);
        $obj =  self::init($v);
        $obj->formatKey($id);
        return $obj;
    }



    public static function init($arr) {
        $obj = new self();
        foreach($arr as $k=>$v) {
            $obj->$k = $v;
        }
        return $obj;
    }

    public function formatKey($key) {
        $this->id = $key;
    }

    public function getKey() {
        return static::getTableName().'-'.$this->id;
    }

    public function getGroupId() {
        return $this->groupId;
    }
    /**
     * 创建memo
     * @param $groupId
     * @param $title
     * @param $content
     * @param $specifyUserId
     * @param $createUserId
     * @param $status
     * @param $dtCreate
     * @param $dtUpdate
     * @return mix
     */
    public static function create($groupId,$title,$content,$specifyUserId,$createUserId,$status,$dtCreate,$dtUpdate) {
        $redis = self::getRedis();
        $id = self::getMaxId();
        $key = static::TABLE_NAME.'-'.$id;
        $redis->setHash($key,'title',$title);
        $redis->setHash($key,'groupId',$groupId);
        $redis->setHash($key,'content',$content);
        $redis->setHash($key,'specifyUserId',$specifyUserId);
        $redis->setHash($key,'createUserId',$createUserId);
        $redis->setHash($key,'status',$status);
        $redis->setHash($key,'dtCreate',$dtCreate);
        $redis->setHash($key,'dtUpdate',$dtUpdate);
        $redis->setString(static::INCREMENT_COUNT_KEY,++$id);
        return $key;
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

    public function setField($field,$value) {
        $this->$field = $value;
    }

    public function update() {
        $redis = self::getRedis();
        $redis->setHashAll($this->getKey(),$this->getFieldArr());
    }

    public function getFieldArr() {
        return [
            'title' => $this->title,
            'groupId' => $this->groupId,
            'content' => $this->content,
            'specifyUserId' => $this->specifyUserId,
            'createUserId' => $this->createUserId,
            'status' => $this->title,
            'dtCreate' => $this->dtCreate,
            'dtUpdate' => $this->dtUpdate,
        ];
    }
}