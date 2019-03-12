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

        $list = $group->getMemoModelList($p,5);
        return $this->render('index.html',[
            'list' => $list,
            'user' => $user,
            'p' => $p,
        ]);
    }
}