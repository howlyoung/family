<?php
/**
 * 用户群组
 */

namespace Model;


class UserGroupModel extends Model
{
    protected $key;
    protected $id;
    protected $name;
    protected $ownerId;
    protected $memberList;
    protected $memoList;
    protected $memberListId;
    protected $memoListId;

    const TABLE_NAME = 'USER_GROUP';

    public static function createGroup($ownerId,$name) {
        $redis = self::getRedis();
        $arr = [
            'ownerId' => $ownerId,
            'name'    => $name,
//            'memberListId' => json_encode([$ownerId]),
//            'memoListId' => '',
        ];
        $max = $redis->getSortSetMax(self::getTableName());

        $id = current($max) + 1;
        $key = self::TABLE_NAME.'-'.$id;
        $arr['memberListId'] = self::TABLE_NAME.'_'.'MEMBER'.'-'.$id;
        $arr['memoListId'] = self::TABLE_NAME.'_'.'MEMO'.'-'.$id;
        //添加散列
        $redis->setHashAll($key,$arr);
        //将key加入用户组的有序集合中
        $redis->setSortSet(self::getTableName(),$key,$id);

        return $key;
    }

    public function getName() {
        return $this->name;
    }

    public function getId() {
        return $this->id;
    }

    public function getMemberList() {
        $redis = self::getRedis();
        $this->memberList = $redis->getAllSortSet($this->memberListId);
        return $this->memberList;
    }

    public function getMemoList() {
        $redis = self::getRedis();
        $this->memoList = $redis->getAllSortSet($this->memoListId);
        return $this->memoList;
    }

    public function getMemberModelList() {
        $redis = self::getRedis();
        $this->memberList = $redis->getAllSortSet($this->memberListId);
        return $this->memberList;
    }

    public function getMemoModelList() {
        $redis = self::getRedis();
        $this->memoList = $redis->getAllSortSet($this->memoListId);
        $arr = [];
        foreach($this->memoList as $memoId) {
            $arr[] = MemoModel::loadByPk($memoId);
        }
        return $arr;
    }

    /**
     * 判断用户是否在组内
     * @param UserModel $user
     * @return bool
     */
    public function userIsMember($user) {
        $memberList = $this->getMemberList();
        return in_array($user->id,$memberList);
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
            $model->id = $id;
            return $model->init($data);
        } else {
            return null;
        }
    }

    /**
     * 通过key来获取
     * @param $key
     * @return $this|UserGroupModel|null
     */
    public static function loadByKey($key) {
        $redis = self::getRedis();

        if($id = $redis->getSortSetScoreByKey(self::getTableName(),$key)) {
            $data = $redis->getHashAll($key);
            $model = new self();
            $model->key = $key;
            $model->id = $id;
            return $model->init($data);
        } else {
            return null;
        }
    }

    /**
     * @return self[]
     */
    public static function loadAll() {
        $redis = self::getRedis();
        $keyList = $redis->getAllSortSet(self::getTableName());
        $arr = [];
        foreach($keyList as $key) {
            $arr[] = self::loadByKey($key);
        }
        return $arr;
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
        $this->getMemberList();
        if(!in_array($memberId,$this->memberList)) {
            array_push($this->memberList,$memberId);
            $redis = self::getRedis();
            $redis->setSortSet($this->memberListId,$memberId,time());
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
        $this->getMemoList();
        if(!in_array($memoId,$this->memoList)) {
            array_push($this->memoList,$memoId);
            $redis = self::getRedis();
            $redis->setSortSet($this->memoListId,$memoId,time());
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
            'memberListId' => $this->memberListId,
            'memoListId' => $this->memoListId,
//            'memberList' => json_encode($this->memberList),
//            'memoList' => json_encode($this->memoList),
        ];
    }

    public function fieldType() {
        return [
            'ownerId' => 'float',
            'name' => 'string',
            'memberListId' => 'string',
            'memoListId' => 'string',
//            'memberList' => 'array',
//            'memoList' => 'array',
        ];
    }

}