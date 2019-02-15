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

    public static function loadByPk($id) {
        $redis = \main::getDb('redis');
        $v = $redis->getHashAll($id);
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
        $arr = explode('-',$key);
        $this->groupId = $arr[0];
        $this->id = $arr[1];
    }

    public function getKey() {
        return $this->groupId.'-'.$this->id;
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
        $redis = \main::getDb('redis');
        $id = rand(0,1999);
        $key = $groupId.'-'.$id;
        $redis->setHash($key,'title',$title);
        $redis->setHash($key,'content',$content);
        $redis->setHash($key,'specifyUserId',$specifyUserId);
        $redis->setHash($key,'createUserId',$createUserId);
        $redis->setHash($key,'status',$status);
        $redis->setHash($key,'dtCreate',$dtCreate);
        $redis->setHash($key,'dtUpdate',$dtUpdate);
        return $key;
    }

    public function setField($field,$value) {
        $this->$field = $value;
    }

    public function update() {
        $redis = \main::getDb('redis');
        $redis->setHashAll($this->getKey(),$this->getFieldArr());
    }

    public function getFieldArr() {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'specifyUserId' => $this->specifyUserId,
            'createUserId' => $this->createUserId,
            'status' => $this->title,
            'dtCreate' => $this->dtCreate,
            'dtUpdate' => $this->dtUpdate,
        ];
    }
}