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

//        $uname = $this->getParam('uname');
//        $user = User::loadByName($uname);

    }
}