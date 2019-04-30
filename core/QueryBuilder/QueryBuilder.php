<?php
namespace core\QueryBuilder;
/**
 * 查询语句构建器
 */
abstract class QueryBuilder
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
    protected $prefix;  //数据表前缀

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
        throw new \Exception('没有可以使用的数据库');
    }

    protected function init() {
        $this->where = '';
        $this->params = [];
        $this->select = [];
        $this->order = '';
        $this->group = '';
        $this->limit = '';
        $this->form = '';
        $this->prefix = $this->getPrefix();
    }

    protected function getPrefix() {
        return '';
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

    public function getCommand() {
        throw new \Exception('无可用的命令');
    }

    /**
     * 查询单行数据
     * @return null
     */
    public function get() {
        return null;
    }

    /**
     * 查询数据集合
     * @return null
     */
    public function getAll(){
        return null;
    }

    /**
     * 执行命令
     * @param $command
     * @param array $params
     * @return mixed
     */
    public function exec($command,$params=[]) {
        return null;
    }
}