<?php
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/2/12
 * Time: 14:34
 */

namespace core\Db;


class DbRedis
{

    protected $conn;    //redis链接对象

    protected $host;    //主机地址
    protected $port;    //端口
    protected $pwd;     //密码

    public function __construct() {
        $this->conn = new \Redis();
    }

    public static function getDb($config) {
        $obj = new self();
        $obj->connect($config);
        return $obj;
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
}