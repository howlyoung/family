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

    /**
     * 获取数据库方法，用于执行语句
     * @return object
     * @throws \Exception
     */
    protected function getDb() {
        return \main::getDb();
    }

    public function __construct($modelClass) {
        parent::__construct();
        $this->modelClass = $modelClass;
    }

    /**
     * 查询单个值
     * @return mixed
     */
    public function get() {
        $row = $this->getDb()->queryRow($this->getCommand(),$this->params);
        $models =  $this->createModel([$row]);
        return reset($models);
    }

    /**
     * 查询多个值，返回数组
     * @return array
     */
    public function getAll() {
        $rows = $this->getDb()->queryAll($this->getCommand(),$this->params);
        return $this->createModel($rows);
    }

    /**
     * 获取sql命令
     * @return string
     */
    public function getCommand() {
        return $this->selectBuild().$this->formBuild().$this->whereBuild($this->where,[]);
    }

    /**
     * 执行语句
     * @param $command
     * @param array $params
     * @return mixed
     */
    public function exec($command,$params=[]) {
        $res =  $this->getDb()->exec($command,$params);
        $this->setErrMsg();
        return $res;
    }

    /**
     * 获取错误信息
     */
    protected function setErrMsg() {
        $msg = $this->getDb()->getErrMsgArr();
        if(!empty($msg)) {
            $this->errMsg = $msg[2];
        }
    }

    /**
     * 构建select语句
     * @return string
     */
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

    /**
     * 构建form语句
     * @return string
     */
    protected function formBuild() {
        if(empty($this->form)) {
            return ' from '.$this->getPrefix().call_user_func([$this->modelClass,'getTableName']);
        } else {
            return $this->form;
        }
    }

    /**
     * 创建MOdel对象
     * @param $rows
     * @return array
     */
    protected function createModel($rows) {
        $models = [];
        foreach($rows as $row) {
            $model = new $this->modelClass;
            $model->fill($row);
            $models[] = $model;
        }
        return $models;
    }

    /**
     * 获取数据库前缀
     * @return mixed
     */
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
        if(null == $this->where) {
            return $this->where($condition,$params);
        } else {
            $this->where = [$this->where,$condition,'and'];
            $this->addParams($params);
            return $this;
        }
    }

    public function orWhere($condition,$params=null) {
        if(null == $this->where) {
            return $this->where($condition,$params);
        } else {
            $this->where = [$this->where,$condition,'or'];
            $this->addParams($params);
            return $this;
        }
    }


    public function addParams($params) {
        if(!empty($params)) {
            $this->params = array_merge($this->params,$params);
        }
    }

    /**
     * 测试方法
     * @return string
     */
    public function tb() {
        return $this->selectBuild().$this->formBuild().$this->whereBuild($this->where,[]);
    }

    /**
     * 获取字段占位符
     * @param $field
     * @return string
     */
    protected function getPlaceholder($field) {
        return ':'.$field;
    }

    /**
     * 根据操作符调用对应的构建方法
     * @param $condition
     * @return string
     * @throws \Exception
     */
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
                    throw new \Exception('操作方法不正确');
                }
            } else {
                //哈希格式
                $tmp = [];
                foreach($condition as $k=>$v) {
                    $tmp[] = $k.'='.(is_numeric($v)?$v:('"'.$v.'"'));
                }
                return implode(' and ',$tmp);
            }
        } else {
            throw new \Exception('条件类型不符合');
        }
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


    /**
     * @param $condition
     * @return string
     * @throws \Exception
     */
    protected function andBuild($condition) {
        $op = strtolower($condition[0]);
        if(!is_array($condition)||(count($condition)!=3)) {
            throw new \Exception('and方法参数不正确!');
        }
        return '('.$this->build($condition[1]).' '.$op.' '.$this->build($condition[2]).')';
    }

    /**
     * @param $condition
     * @return string
     * @throws \Exception
     */
    protected function inBuild($condition) {
        $op = strtolower($condition[0]);
        if(!is_array($condition)||(count($condition)!=3)) {
            throw new \Exception('in方法参数不正确!');
        }
        return '('.$condition[1].' '.$op.' ('.implode(',',$condition[2]).'))';
    }

    protected function betweenBuild($condition) {
        if(!is_array($condition)||(count($condition)!=3)) {
            throw new \Exception('between方法参数不正确!');
        }
        return '('.$condition[1].' > '.$condition[2][0].' and '.$condition[1].' < '.$condition[2][1];
    }

    /**
     * @param $condition
     * @return string
     * @throws \Exception
     */
    protected function orBuild($condition) {
        $op = strtolower($condition[0]);
        if(!is_array($condition)||(count($condition)!=3)) {
            throw new \Exception('or方法参数不正确!');
        }
        return '('.$this->build($condition[1]).' '.$op.' '.$this->build($condition[2]).')';
    }
}