<?php
/**
*记事本的控制器
 */

namespace Controller;

use component\Module\User;
use Model\UserGroupModel;


class MemoController extends BaseController
{
    public function index() {
        $gid = 1;
        $group = UserGroupModel::loadById($gid);
        $user = \main::getUser();

        var_dump($group);
//        $uname = $this->getParam('uname');
//        $user = User::loadByName($uname);

    }
}