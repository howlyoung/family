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
    /**
     * ЪєадзжЖЮ
     */
    public $id;
    public $name;
    public $password;
    public $remark;
    public $dt_create;

    public function __construct() {

    }

}