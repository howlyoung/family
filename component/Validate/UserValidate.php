<?php
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/1/11
 * Time: 13:50
 */

namespace component\Validate;


class UserValidate extends Validate
{
    public function verifyPassword($pwd) {
        return md5($pwd);
    }
}