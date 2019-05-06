<?php
namespace Model;
use core\Model\DBModel\DBModel;

/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2018/12/24
 * Time: 10:05
 */
class UserModel extends DBModel
{
    /**
     * 属性字段
     */
//    public $id;
//    public $name;
//    public $password;
//    public $remark;
//    public $dt_create;

    const TABLE_NAME = 'user';


    /**
     * @param $pk
     * @return mixed
     */
    public static function loadByPk($pk) {
        return self::table()->select(['*'])->from(self::getTableName())->where(['id'=>$pk])->get();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public static function loadByField(Array $params) {
        return self::table()->select(['*'])->from(self::getTableName())->where($params)->get();
    }

    public function getName() {
        return $this->name;
    }

    public function attributes() {
        return [
            'id' => 'primary',
            'name' => '',
            'password' => '',
            'remark' => '',
//            'tt' => '',
            'dt_create' => '',
        ];
    }
}