<?php
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/4/16
 * Time: 9:02
 */

namespace core\Query;


use core\QueryBuilder\QueryBuilder;

trait DBQueryTrait
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