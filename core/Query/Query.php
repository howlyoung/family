<?php
namespace core\Query;
use core\QueryBuilder\QueryBuilder;

/**
 * 查询器，用来进行SQL查询，获得结果
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/4/15
 * Time: 14:45
 */
class Query
{
    /** @var \core\QueryBuilder\QueryBuilder $builder */
    protected $_builder;     //构建器对象

    public function all() {

    }

    public function count() {

    }

    protected function getBuilder() {
        if(empty($this->_builder)) {
            $this->_builder = \main::getContainer()->get('core\QueryBuilder\QueryBuilder');
        }
        return $this->_builder;
    }
}