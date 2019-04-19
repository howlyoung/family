<?php
namespace core\Model\DBModel;
use core\Model\Model;

/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/4/19
 * Time: 9:28
 */
class DBModel extends Model
{

    protected static function table() {
        return \main::getContainer()->get('core\QueryBuilder\ModelQueryBuilder',['modelClass' => get_called_class()]);
    }

    public function save() {
        if($this->getIsNewRecord()) {
            //新记录，插入

        } else {
            //保存
        }
    }
}