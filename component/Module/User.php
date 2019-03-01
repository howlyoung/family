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

    /**
     * @param \Model\MemoModel $memoModel
     * @return bool
     */
    public function createMemo($memoModel) {
        return $this->model->createMemo($memoModel);
    }
}