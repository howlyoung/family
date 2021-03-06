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
        $p = $this->getParam('p',1);
        $group = UserGroupModel::loadById($gid);
        $user = \main::getUser();

        $size = 1;
        $list = $group->getMemoModelList($p,$size);
        return $this->render('index',[
            'list' => $list,
            'user' => $user,
            'p' => $p,
            'size' => $size,
        ]);
    }
}