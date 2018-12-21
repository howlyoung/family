<?php

namespace core\Db;
/**
*pdo
 */
class Db
{
    protected $host;    //主机名称
    protected $dbname;  //数据库名称
    protected $user;    //用户名
    protected $password;    //连接密码
    protected $type;    //数据库类型

    protected $conn;        //链接

    public function __construct($config) {
        if($this->checkParms($config)) {
            $this->host = $config['host'];
            $this->dbname = $config['dbname'];
            $this->user = $config['user'];
            $this->password = $config['password'];
        }
    }

    public function connect() {
        $this->conn = new \PDO($this->type.':host='.$this->host,$this->user,$this->password,[\PDO::ATTR_PERSISTENT => true]);
    }

    public function query($sql,$params) {

    }

    /**
     * @param $config
     * @return bool
     * @throws \Exception
     */
    protected function checkParms($config) {
        if(!is_array($config)) {
            throw new \Exception('数据库配置的参数不正确');
        }

        $fieldArr = $this->getConfigField();
        //检查配置项是否齐全
        foreach($config as $k=>$v) {
            if(array_key_exists($k,$fieldArr)) {
                unset($fieldArr[$k]);
            }
        }
        if(empty($fieldArr)) {
            return true;
        } else {
            $msg = implode('_',array_flip($fieldArr));
            throw new \Exception('数据库配置缺少'.$msg);
        }
    }

    /**
     * 配置项应有的字段
     * @return array
     */
    protected function getConfigField() {
        return [
            'host'=>'1',
            'dbname'=>'2',
            'user'=>'3',
            'password'=>'4',
            'type' => '5',
        ];
    }
}