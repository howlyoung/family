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

    public function getSql() {
        return $this->selectBuild().$this->formBuild().$this->whereBuild($this->where,[]);
    }

    protected function selectBuild() {
        if(empty($this->select)) {
            return  'select * ';
        } else {
            if(is_array($this->select)) {
                return 'select '.implode(',',$this->select);
            } else {
                return 'select '.$this->select;
            }
        }
    }

    protected function formBuild() {
        if(empty($this->form)) {
            return ' from '.$this->getPrefix().call_user_func([$this->modelClass,'getTableName']);
        } else {
            return $this->form;
        }
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

    protected function getPrefix() {
        return \main::getConfig('db.db.prefix');
    }


    public function select($field) {
        $this->select = $field;
        return $this;
    }

    public function from($table) {
        $this->form = ' FROM '.$this->prefix.$table;
        return $this;
    }

    public function where($condition,$params=null) {
        $this->where = [null,$condition];
        $this->addParams($params);
        return $this;
    }

    public function andWhere($condition,$params=null) {
        $this->where = [$this->where,$condition,'and'];
        $this->addParams($params);
        return $this;
    }

    public function orWhere($condition,$params=null) {
        $this->where = [$this->where,$condition,'or'];
        $this->addParams($params);
        return $this;
    }

    public function inWhere($condition,$params=null) {
        $this->where = [$this->where,$condition,'in'];
        $this->addParams($params);
        return $this;
    }

    public function betweenWhere($condition,$params=null) {
        $this->where = [$this->where,$condition,'between'];
        $this->addParams($params);
        return $this;
    }

    public function addParams($params) {
        if(!empty($params)) {
            $this->params = array_merge($this->params,$params);
        }
    }


    public function tb() {
        return $this->selectBuild().$this->formBuild().$this->whereBuild($this->where,[]);
    }

    protected function getPlaceholder($field) {
        return ':'.$field;
    }

    /**
     * 处理条件，可能的形式有 ['a'=>'b','c'=>'d','e'=>'f'],['操作符','操作数','操作数']，字符串格式
     * @param $where
     * @param $sql
     * @return array|mixed
     */
    protected function whereBuild($where,$sql) {
        $nextWhere = $where[0];
        $condition = $where[1];
        $ope = isset($where[2])?$where[2]:'';

        $sql[] = [$this->build($condition),$ope];
        if($nextWhere == null) {
            $str = '';
            foreach($sql as $s) {
                $str .= ' ('.$s[0].') '.$s[1];
            }
            return ' where '.$str;
        } else {
            return call_user_func_array([$this,__FUNCTION__],[$nextWhere,$sql]);
        }
    }

    protected function build($condition)
    {
        if (is_string($condition)) {
            return $condition;
        } elseif (is_array($condition)) {
            if(isset($condition[0])) {
                //操作数格式
                $opMethod = strtolower($condition[0]).'Build';
                if(method_exists($this,$opMethod)) {
                    return $this->$opMethod($condition);
                } else {
                    throw new \Exception('操作数不正确');
                }
            } else {
                //哈希格式
                $tmp = [];
                foreach($condition as $k=>$v) {
                    $tmp[] = $k.'='.$v;
                }
                return implode(' and ',$tmp);
            }
        }
    }

    protected function andBuild($condition) {
        $op = strtolower($condition[0]);
        return '('.$this->build($condition[1]).' '.$op.' '.$this->build($condition[2]).')';
    }

    protected function inBuild($condition) {
        $op = strtolower($condition[0]);
        return '('.$this->build($condition[1]).' '.$op.' '.$this->build($condition[2]).')';
    }

    protected function orBuild($condition) {
        $op = strtolower($condition[0]);
        return '('.$this->build($condition[1]).' '.$op.' '.$this->build($condition[2]).')';
    }
}