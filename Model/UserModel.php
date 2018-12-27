<?php
namespace Model;
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2018/12/24
 * Time: 10:05
 */
class UserModel extends Model
{
    public function __construct($field=[]) {
        foreach($field as $k=>$v) {
            $this->k = $v;
        }

    }
}