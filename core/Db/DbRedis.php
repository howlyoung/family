<?php
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/2/12
 * Time: 14:34
 */

namespace core\Db;


class DbRedis implements DbInterface
{

    protected $conn;    //redis链接对象

    protected $host;    //主机地址
    protected $port;    //端口
    protected $pwd;     //密码

    public function __construct() {
        $this->conn = new \Redis();
    }

    /**
     * @param $config
     * @return $this
     */
    public function getDb($config) {
        $this->connect($config);
        return $this;
    }

    public function connect($config) {
        if($this->checkParams($config)) {
            $this->conn->pconnect($this->host,$this->port,1);
//            if(!$this->conn->auth($this->pwd)) {
//                return null;
//            }
        } else {
            return null;
        }
    }

    protected function checkParams($params) {
        $arr = $this->getConfigField();
        foreach($params as $k=>$v) {
            if(array_key_exists($k,$arr)) {
                $this->$arr[$k] = $params[$k];
                unset($arr[$k]);
            }
        }
        if(empty($arr)) {
            return true;
        } else {
            $msg = implode('_',array_flip($arr));
            throw new \Exception('redis数据库配置缺少'.$msg);
        }
    }

    public function getConnect() {
        return $this->conn;
    }

    /**
     * 配置文件应有字段
     * @return array
     */
    protected function getConfigField() {
        return [
            'host'=>'host',
//            'dbname'=>'2',
            'port'=>'port',
            'password'=>'pwd',
//            'type' => '5',
        ];
    }

    public function setHash($key,$field,$val) {
        $this->conn->hSet($key,$field,$val);
    }

    public function getHashField($key,$field) {

    }

    public function setHashAll($key,$hash) {
        $this->conn->hMset($key,$hash);
    }

    public function getHashAll($key) {
        return $this->conn->hGetAll($key);
    }

    public function setSet($key,$val) {
        return $this->conn->sadd($key,$val);
    }

    public function getAllSet($key) {
        return $this->conn->sMembers($key);
    }

    public function setSortSet($key,$k,$val) {
        $this->conn->zAdd($key,$val,$k);
    }

    public function getSortSetByKey($key,$k) {
//        return $this->conn->
    }

    public function getSortSetScoreByKey($key,$k) {
        return $this->conn->zScore($key,$k);
    }

    /**
     * 获取有效集合最大值的键
     * @param $key
     * @return array
     */
    public function getSortSetMax($key) {
        return $this->conn->zRevRangeByScore($key, '+inf','-inf',['withscores' => true,'limit' => [0,1]]);
    }

}