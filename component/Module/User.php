<?php
namespace component\Module;
use component\Validate\Validate;
use Model\UserGroupModel;
use Model\UserModel;

/**
 */
class User
{
    protected $model;   //数据表
    protected $validateType; //验证类型


    public function __construct($userModel)
    {
        try {
            if ($userModel instanceof UserModel) {
                $this->model = $userModel;
            } else {
                throw new \Exception('必须是usermodel类型');
            }
        } catch(\Exception $e) {
            echo $e->getMessage();
            exit;
        }
        $this->validateType = 'User';
    }

    /**
     * @param $name
     * @return User|null
     */
    public static function loadByName($name) {
        $user = UserModel::loadByField(['name'=>$name]);    //优化，此处与表字段耦合
        if(empty($user)) {
            return null;
        } else {
            return new self($user);
        }
    }

    /**
     * @param $id
     * @return User|null
     */
    public static function loadById($id) {
        $user = UserModel::loadByField(['id'=>$id]);//优化，此处与表字段耦合
        if(empty($user)) {
            return null;
        } else {
            return new self($user);
        }
    }

    /**
     * @param $password
     * @return bool
     */
    public function verifyPwd($password) {
        return ($this->model->password == Validate::getValidate($this->validateType)->verifyPassword($password));
    }

    /**
     * @return bool
     */
    public function login() {
        $_SESSION['uid'] = $this->model->id;
        return true;
    }

    /**
     *
     */
    public function logout() {
        session_destroy();
    }

    public function getName() {
        return $this->model->getName();
    }

    /**
     * @param \Model\MemoModel $memoModel
     * @return bool
     */
    public function createMemo($memoModel) {
        $groupModel = UserGroupModel::loadById($memoModel->getGroupId());
        if(!$groupModel->userIsMember($this->model)) {
            $memoModel->setErrMsg('用户不在该群组中');
            return false;
        } else {
            $memoModel->save();
            $memoModel->setCreateId($this->model->id);
            return true;
        }
    }

    /**
     * @return array
     */
    public function getGroupList() {
        $groupList = UserGroupModel::loadAll();
        $arr = [];
        foreach($groupList as $group) {
            if($group->userIsMember($this->model)) {
                $arr[] = $group;
            }
        }
        return $arr;
    }
}