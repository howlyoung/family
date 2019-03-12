<?php
namespace Model;
use core\QueryBuilder\QueryBuilder;

/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2018/12/24
 * Time: 10:05
 */
class UserModel extends Model
{
    /**
     * 属性字段
     */
    public $id;
    public $name;
    public $password;
    public $remark;
    public $dt_create;

    const TABLE_NAME = 'user';

    public function __construct() {

    }

    /**
     * @param $pk
     * @return mixed
     */
    public static function loadByPk($pk) {
        return self::table()->select(['*'])->from(self::getTableName())->where(['id','=',$pk])->get();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public static function loadByField(Array $params) {
        $where = [];
        foreach($params as $k=>$v) {
            $where[] = [$k,'=',$v];
        }
        return self::table()->select(['*'])->from(self::getTableName())->where($where)->get();
    }

    public function getName() {
        return $this->name;
    }
}