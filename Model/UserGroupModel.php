<?php
/**
 * 用户群组
 */

namespace Model;
use core\Model\RedisModel\RedisModel;


class UserGroupModel extends RedisModel
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
        $redis = self::table();
        $arr = [
            'ownerId' => $ownerId,
            'name'    => $name,
        ];
        $max = $redis->getSortSetMax(self::getTableName());

        if(is_null($max)) {
            return null;
        }
        $id = current($max) + 1;
        $key = self::getKeyById($id);
        $arr['memberListId'] = self::getMemberIdListKeyById($id);
        $arr['memoListId'] = self::getMemoIdListKeyById($id);
        //添加散列
        $model = null;
        if($redis->setHashAll($key,$arr)) {
            $model = self::loadById($id);
            //将key加入用户组的有序集合中
            $redis->setSortSet(self::getTableName(),$key,$id);
        }
        return $model;
    }

    /**
     * 通过id获取key
     * @param $id
     * @return string
     */
    public static function getKeyById($id) {
        return self::TABLE_NAME.'-'.$id;
    }

    /**
     * 获取会员列表的key
     * @param $id
     * @return string
     */
    public static function getMemberIdListKeyById($id) {
        return self::TABLE_NAME.'_'.'MEMBER'.'-'.$id;
    }

    /**
     * 获取memo列表的Key
     * @param $id
     * @return string
     */
    public static function getMemoIdListKeyById($id) {
        return self::TABLE_NAME.'_'.'MEMO'.'-'.$id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @param $page
     * @param $size
     * @return array
     */
    public function getMemberList($page=0,$size=10) {
        return empty($this->memberList)?$this->query->getAllSortSet($this->memberListId,$page,$size):$this->memberList;
    }

    /**
     * @param $page
     * @param $size
     * @return array
     */
    public function getMemoList($page=0,$size=10) {
        return empty($this->memoList)?$this->query->getAllSortSet($this->memoListId,$page,$size):$this->memoList;
    }

    /**
     * @param $page
     * @param $size
     * @return UserModel[]
     */
    public function getMemberModelList($page=0,$size=10) {
        $this->memberList = $this->getMemberList($page,$size);
        $arr = [];
        foreach($this->memberList as $memberId) {
            $arr[] = UserModel::loadByPk($memberId);
        }
        return $arr;
    }

    /**
     * @param $page
     * @param $size
     * @return MemoModel[]
     */
    public function getMemoModelList($page=0,$size=10) {
        $this->memoList = $this->getMemoList($page,$size);
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
        $key = self::getKeyById($id);
        return self::loadByKey($key);
    }

    /**
     * 通过key来获取
     * @param $key
     * @return $this|UserGroupModel|null
     */
    public static function loadByKey($key) {
        $redis = self::table();

        if($id = $redis->getSortSetScoreByKey(self::getTableName(),$key)) {
            $data = $redis->getHashAll($key);
            $model = new self();
            return $model->init($key,$id,$data);
        } else {
            return null;
        }
    }

    /**
     * @return self[]
     */
    public static function loadAll() {
        $keyList = self::table()->getAllSortSet(self::getTableName());
        $arr = [];
        foreach($keyList as $key) {
            $arr[] = self::loadByKey($key);
        }
        return $arr;
    }

    /**
     * @param $key
     * @param $id
     * @param $data
     * @return $this
     */
    public function init($key,$id,$data) {
        foreach($data as $k=>$v) {
            $this->$k = $v;
        }
        $this->key = $key;
        $this->id = $id;
//        $this->memberList = $this->getMemberList();
//        $this->memoList = $this->getMemoList();
        return $this;
    }

    /**
     * @param $memberId
     * @return bool
     */
    public function addMember($memberId) {  //参数可考虑使用user对象
        $list = $this->getMemberList();
        if(!in_array($memberId,$list)) {
//            array_push($this->memberList,$memberId);
            $res = $this->query->setSortSet($this->memberListId,$memberId,time());
            return (0<$res);
        } else {
            return false;
        }

    }

    /**
     * @param $memoId
     * @return bool
     */
    public function addMemo($memoId) {  //参数可考虑使用memo对象
        $list = $this->getMemoList();
        if(!in_array($memoId,$list)) {
//            array_push($this->memoList,$memoId);
            $res = $this->query->setSortSet($this->memoListId,$memoId,time());
            return (0<$res);
        } else {
            return false;
        }
    }

    public function save() {
        $arr = $this->getFieldArr();
        $redis = self::getRedis();
        $this->query->setHashAll($this->key,$arr);
    }

    public function getFieldArr() {
        return [
            'ownerId' => $this->ownerId,
            'name' => $this->name,
            'memberListId' => $this->memberListId,
            'memoListId' => $this->memoListId,
        ];
    }

}