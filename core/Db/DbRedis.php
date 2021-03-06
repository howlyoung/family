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
        try {
            if($this->checkParams($config)) {
                if(!$this->conn->pconnect($this->host,$this->port,1)) {
                    throw new \Exception('连接失败,请检查服务器配置项和服务器是否开启');
                }
            }
        } catch(\Exception $e) {
            echo $e->getMessage();
            exit;
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

    public function setString($key,$val) {
        $this->conn->set($key,$val);
    }

    public function getString($key) {
        return $this->conn->get($key);
    }

    public function setHash($key,$field,$val) {
        $this->conn->hSet($key,$field,$val);
    }

    public function getHashField($key,$field) {

    }

    public function setHashAll($key,$hash) {
        return $this->conn->hMset($key,$hash);
    }

    public function getHashAll($key) {
        return $this->conn->hGetAll($key);
    }

    public function setSet($key,$val) {
        return $this->conn->sadd($key,$val);
    }

    public function moveSet($sourceKey,$targetKey,$key) {
        return $this->conn->sMove($sourceKey,$targetKey,$key);
    }

    public function getAllSet($key) {
        return $this->conn->sMembers($key);
    }

    public function setSortSet($key,$k,$val) {
        return $this->conn->zAdd($key,$val,$k);
    }

    public function getSortSetByKey($key,$k) {
//        return $this->conn->
    }

    public function getSortSetScoreByKey($key,$k) {
        return $this->conn->zScore($key,$k);
    }

    /**
     * 获取有序集合元素，带分页
     * @param $key
     * @param int $p
     * @param int $size
     * @return array
     */
    public function getAllSortSet($key,$p=0,$size=15) {
        $option = [];
        if(0 < $p) {
            $option['limit'] = [($p-1)*$size,$size];
        }
        return $this->conn->zRevRangeByScore($key,'+inf','-inf',$option);
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