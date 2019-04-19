<?php
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/4/17
 * Time: 16:04
 */

namespace core\QueryBuilder;

/**
 * MODEL使用的查询器，需要model类名为参数，查询返回的结果就是model的实例
 * Class ModelQueryBuilder
 * @package core\QueryBuilder
 */
class ModelQueryBuilder extends QueryBuilder
{
    protected $modelClass;

    public function __construct($modelClass) {
        parent::__construct();
        $this->modelClass = $modelClass;
    }

    public function get() {
        $row = $this->getDb()->queryRow($this->getSql(),$this->params);
        $models =  $this->createModel([$row]);
        return reset($models);
    }


    public function getAll() {
        $rows = $this->getDb()->queryAll($this->getSql(),$this->params);
        return $this->createModel($rows);
    }


    protected function createModel($rows) {
        $models = [];
        foreach($rows as $row) {
            $model = new $this->modelClass;
            $model->fill($row);
            $models[] = $model;
        }
        return $models;
    }
}