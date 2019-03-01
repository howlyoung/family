<?php
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/1/14
 * Time: 8:57
 */

namespace Controller;

use component\Module\User;
use Model\MemoModel;

class UserController extends BaseController
{

    public function login() {
        return $this->render('login.html',[]);
    }

    public function doLogin() {
        $uname = $this->postParam('uname');
        $pwd = $this->postParam('pwd');

        $user = User::loadByName($uname);
        if(empty($user)) {
            return '用户不存在';
        }
        if(!$user->verifyPwd($pwd)) {
            return '密码不正确';
        }
        $user->login();
        return '登录成功';
    }

    public function logout() {
        $user = \main::getUser();
        if(!empty($user)) {
            $user->logout();
        }
        return '已经退出';
    }

    public function createMemo() {
        $gid = $this->getParam('gid');

        $user = \main::getUser();

        $title = $this->getParam('title');
        $content = $this->getParam('content');
        $specifyUserId = $this->getParam('specifyUserId');
        $status = $this->getParam('status');

        $params = [
            'groupId' => $gid,
            'title' => $title,
            'content' => $content,
            'specifyUserId' => $specifyUserId,
            'status' => $status,
        ];
        $memo = MemoModel::init($params);
        if(!$user->createMemo($memo)) {
            return $memo->getErrMsg();
        }


    }
}