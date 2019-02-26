<?php
/**
 * 用户群组
 */

namespace Model;


class UserGroupModel extends Model
{
    protected $key;
    protected $name;
    protected $ownerId;
    protected $memberList;
    protected $memoList;

    const TABLE_NAME = 'USER_GROUP';

    public static function createGroup($ownerId,$name) {
        $redis = self::getRedis();
        $arr = [
            'ownerId' => $ownerId,
            'name'    => $name,
            'memberList' => json_encode([$ownerId]),
            'memoList' => '',
        ];
        $max = $redis->getSortSetMax(self::getTableName());

        $id = current($max) + 1;
        $key = self::TABLE_NAME.'-'.$id;
        //添加散列
        $redis->setHashAll($key,$arr);
        //将key加入用户组的有序集合中
        $redis->setSortSet(self::getTableName(),$key,$id);

        return $key;
    }

    /**
     * @param $id
     * @return $this|UserGroupModel|null
     */
    public static function loadById($id) {
        $redis = self::getRedis();
        $key = self::getTableName().'-'.$id;
        if($redis->getSortSetScoreByKey(self::getTableName(),$key) == $id) {
            $data = $redis->getHashAll($key);
            $model = new self();
            $model->key = $key;
            return $model->init($data);
        } else {
            return null;
        }
    }

    public function init($data) {
        $arr = $this->fieldType();
        foreach($data as $k=>$v) {
            if(array_key_exists($k,$arr)) {
                if($arr[$k]=='array') {
                    $this->$k = json_decode($v,true);
                } else {
                    $this->$k = $v;
                }
            }
        }
        return $this;
    }

    /**
     * @param $memberId
     * @return bool
     */
    public function addMember($memberId) {  //参数可考虑使用user对象
        if(!in_array($memberId,$this->memberList)) {
            array_push($this->memberList,$memberId);
            $this->save();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $memoId
     * @return bool
     */
    public function addMemo($memoId) {  //参数可考虑使用memo对象
        if(!in_array($memoId,$this->memoList)) {
            array_push($this->memoList,$memoId);
            $this->save();
            return true;
        } else {
            return false;
        }
    }

    public function save() {
        $arr = $this->getFieldArr();
        $redis = self::getRedis();
        $redis->setHashAll($this->key,$arr);
    }

    public function getFieldArr() {
        return [
            'ownerId' => $this->ownerId,
            'name' => $this->name,
            'memberList' => json_encode($this->memberList),
            'memoList' => json_encode($this->memoList),
        ];
    }

    public function fieldType() {
        return [
            'ownerId' => 'float',
            'name' => 'string',
            'memberList' => 'array',
            'memoList' => 'array',
        ];
    }

}