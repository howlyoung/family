<?php
namespace core\QueryBuilder;
/**
 * 查询语句构建器
 */
class QueryBuilder
{
    /**

     */
    protected $where;   //查询条件语句
    protected $params; //查询语句绑定的参数
    protected $select;   //查询字段语句
    protected $order;   //排序
    protected $group;   //分组
    protected $limit;   //limit限制
    protected $form;

    protected $db;      //数据库连接

    public function test() {
        print_r($this->where);
        print_r($this->params);
    }

    public function __construct() {
        $this->init();
    }

    /**
     * 获取数据库方法，用于执行语句
     * @return object
     * @throws \Exception
     */
    protected function getDb() {
        return \main::getDb();
    }

    protected function init() {
        $this->where = '';
        $this->params = [];
        $this->select = [];
        $this->order = '';
        $this->group = '';
        $this->limit = '';
        $this->form = '';
    }

    /**
     * [[field,op,value]]
     * @param $params
     * @throws \Exception
     */
    protected function andSplit($params) {
        $tmp = [];
        foreach($params as $v) {
            if(!is_array($v)||!(3==count($v)||!is_string($v[0])||!is_string($v[1])||!is_string($v[2]))) {
                throw new \Exception('where方法参数不正确');
            }
            //占位符
            $placeholder = ':'.$v[0];
            $this->params[$placeholder] = $v[2];
            $v[2] = $placeholder;
            $tmp[] = implode(' ',$v);
        }
        $op = empty($this->where)?'':' and ';
        $this->where .= $op.'('.implode(' and ',$tmp);
    }

    protected function orSplit($params) {
        $tmp = [];
        foreach($params as $v) {
            if(!is_array($v)||!(3==count($v)||!is_string($v[0])||!is_string($v[1])||!is_string($v[2]))) {
                throw new \Exception('where方法参数不正确');
            }
            $placeholder = ':'.$v[0];
            $this->params[$placeholder] = $v[2];
            $v[2] = $placeholder;
            $tmp[] = implode(' ',$v);
        }
        $op = empty($this->where)?'':' or ';
        $this->where .= $op.'('.implode(' and ',$tmp);
    }

    protected function inSplit($params) {
        $tmp = [];
        foreach($params as $v) {
            if(!is_array($v)||!(2==count($v)||!is_string($v[0])||!is_array($v[1]))) {
                throw new \Exception('in方法参数不正确');
            }
            $placeholder = ':'.$v[0];
            $this->params[$placeholder] = implode(',',$v[1]);
            $v[1] = $placeholder;
            $tmp[] = implode(' in ',$v);
        }
        $op = empty($this->where)?'':' and ';
        $this->where .= $op.'('.implode(' and ',$tmp);
    }

    protected function betweenSplit($params) {
        $sql = [];
        foreach($params as $v) {
            if(!is_array($v)||!(3==count($v)||!is_string($v[0])||!is_array($v[1])||!is_array($v[2]))) {
                throw new \Exception('between方法参数不正确');
            }
            $tmp = [];
            $tmp[] = $v[0].'>'.' :1'.$v[0];
            $tmp[] = $v[0].'<'.' :2'.$v[0];
            $this->params[' :1'.$v[0]] = $v[1];
            $this->params[' :2'.$v[0]] = $v[2];

            $sql[] = '('.implode(' and ',$tmp).')';
        }
        $and = empty($this->where)?'':' and ';
        $this->where .= $and.'('.implode(' and ',$sql);
    }

    protected function likeSplit() {

    }

    /**
     * 处理输入参数，将输入参数统一成[[],[],[]]的形式，方便下一步处理
     * @param $method
     * @param $field
     * @param null $option
     * @param null $value
     * @return $this
     */
    protected function process($method,$field,$option=null,$value=null) {
        try {
            $tmp = [];
            $methodName = $method.'Split';
            if(!method_exists($this,$methodName)) {
                throw new \Exception('调用的方法不存在');
            }
            if(is_array($field)) {
                //数组形式的参数
                if(!is_array($field[0])) {
                    //第一个参数不是数组，则不是多个字段一起操作
                    $tmp[] = $field;
                    $this->$methodName($tmp);
                } else {
                    foreach($field as $v) {
                        if(is_array($v)) {
                            $tmp[] = $v;
                        } else {
                            throw new \Exception('参数不正确');
                        }
                    }
                    $this->$methodName($tmp);
                }

                if(!empty($option)) {
                    if(is_callable($option)) {
                        call_user_func($option,$this);
                    } else {
                        throw new \Exception('回调方法不正确');
                    }
                }
            } else {
                $tmp[] = [$field,$option,$value];
                $this->$methodName($tmp);
            }
            $this->where .= ')';
            return $this;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * 设置查询的where条件,链式操作的开始函数
     * where('field','>','value')->where('field','>','value') => field > value and field > value
     * where([['field','>','value'],['field','>','value']]) => field > value and field > value
     * where('field','>','value')->orWhere('field','>','value') => field > value or field > value
     *
     * where([['field','>','value']],function($obj) {})  field > value or (field > value and field > value)
     * @param $field
     * @param $option
     * @param $value
     * @return $this|null
     */
    public function where($field,$option=null,$value=null) {
        $this->process('and',$field,$option,$value);
        return $this;
    }

    /**
     * @param $field
     * @param null $option
     * @param null $value
     * @return $this
     */
    public function orWhere($field,$option=null,$value=null) {
        $this->process('or',$field,$option,$value);
        return $this;
    }

    /**
     * @param $field
     * @param null $option
     * @param null $value
     * @return $this
     */
    public function inWhere($field,$option=null,$value=null) {
        $this->process('in',$field,$option,$value);
        return $this;
    }

    /**
     * @param $field
     * @param null $option
     * @param null $value
     * @return $this
     */
    public function betweenWhere($field,$option=null,$value=null) {
        $this->process('between',$field,$option,$value);
        return $this;
    }

    /**
     * @param $order
     * @param string $dir
     * @return $this
     */
    public function order($order,$dir='desc') {
        try{
            if(!is_array($order)||('desc'!=$dir&&'asc'!=$dir)) {
                throw new \Exception('order参数不正确');
            } else {
                foreach($order as $k=>$v) {
                    if(!is_string($v)) {
                        throw new \Exception('order字段不正确');
                    }
                    $order[$k] .= ' '.$dir;
                }
                $this->order = implode(',',$order);
                return $this;
            }
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $p
     * @param $size
     * @return $this
     */
    public function limit($p,$size) {
        $this->limit = ':p ,:size';
        $this->params[':p'] = $p;
        $this->params[':size'] = $size;
        return $this;
    }

    public function getSql() {
        return $this->select.$this->form.' WHERE '.$this->where.$this->order.$this->limit;
    }

    public function get() {
        return $this->getDb()->queryAll($this->getSql(),$this->params);
    }

    public function getArray() {
        return $this->getDb()->queryAll($this->getSql(),$this->params);
    }

    /**
     * @param $field
     * @return $this|null
     */
    public function select($field) {
        try{
            if(!is_array($field)) {
                throw new \Exception('参数不符合');
            } else {
                $this->select = 'SELECT '.implode(',',$field);
                return $this;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function from($table) {
        $prefix = \main::getConfig('db.db.prefix');
        $this->form = ' FROM '.$prefix.$table;
        return $this;
    }


}